<?php

namespace App\Http\Controllers\Klinik;

use App\Exports\Klinik\ReportBerkalaExport;
use App\Exports\Klinik\ReportKunjunganExport;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KunjunganKlinik;
use App\Models\PemeriksaanBerkala;
use App\Models\Periode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * ============================================================
     * REPORT PEMERIKSAAN BERKALA
     * ============================================================
     */
    public function berkala(Request $request)
    {
        $periodeId = $request->input('periode_id');

        $periode = $periodeId
            ? Periode::find($periodeId)
            : Periode::where('status', 'aktif')->first();

        $periodeList = Periode::query()
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_periode' => $item->nama_periode,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'status' => $item->status,
                ];
            })
            ->values();

        if (!$periode) {
            return Inertia::render(
                'Klinik/Kesehatan/Report/Berkala',
                [
                    'periode' => null,
                    'periodeList' => $periodeList,
                    'siswa' => [],
                    'statistik' => [
                        'total_siswa' => 0,
                        'berkala_1_selesai' => 0,
                        'berkala_1_belum' => 0,
                        'berkala_2_selesai' => 0,
                        'berkala_2_belum' => 0,
                        'lengkap' => 0,
                        'belum_lengkap' => 0,
                        'belum_diperiksa' => 0,
                    ],
                    'kelas' => [],
                    'filter' => [
                        'kelas_id' => $request->input('kelas_id'),
                    ],
                ]
            );
        }

        $kelasId = $request->input('kelas_id');

        /*
        |--------------------------------------------------------------------------
        | SISWA DALAM PERIODE
        |--------------------------------------------------------------------------
        */

        $querySiswa = $periode->siswa()
            ->with([
                'kelas.jurusan',
                'pemeriksaanBerkala' => function ($query) use ($periode) {
                    $query
                        ->where('periode_id', $periode->id)
                        ->whereIn(
                            'jenis_pemeriksaan',
                            ['berkala_1', 'berkala_2']
                        )
                        ->with('pemeriksa');
                },
            ]);

        if ($kelasId) {
            $querySiswa->where('kelas_id', $kelasId);
        }

        $siswas = $querySiswa
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA SISWA
        |--------------------------------------------------------------------------
        */

        $dataSiswa = $siswas->map(function ($siswa) {

            $berkala1 = $siswa->pemeriksaanBerkala
                ->where('jenis_pemeriksaan', 'berkala_1')
                ->sortByDesc('id')
                ->first();

            $berkala2 = $siswa->pemeriksaanBerkala
                ->where('jenis_pemeriksaan', 'berkala_2')
                ->sortByDesc('id')
                ->first();

            if (
                $berkala1 &&
                $berkala1->status === 'selesai' &&
                $berkala2 &&
                $berkala2->status === 'selesai'
            ) {
                $statusKeseluruhan = 'lengkap';
            } elseif ($berkala1 || $berkala2) {
                $statusKeseluruhan = 'belum_lengkap';
            } else {
                $statusKeseluruhan = 'belum_diperiksa';
            }

            return [
                'id' => $siswa->id,

                'siswa' => [
                    'id' => $siswa->id,
                    'nisn' => $siswa->nisn,
                    'nama' => $siswa->nama,

                    'kelas' => $siswa->kelas
                        ? [
                            'id' => $siswa->kelas->id,
                            'nama_kelas' => $siswa->kelas->nama_kelas,
                        ]
                        : null,

                    'jurusan' => $siswa->kelas?->jurusan
                        ? [
                            'id' => $siswa->kelas->jurusan->id,
                            'nama_jurusan' =>
                                $siswa->kelas->jurusan->nama_jurusan,
                        ]
                        : null,
                ],

                'berkala_1' => $berkala1
                    ? $this->formatPemeriksaan($berkala1)
                    : null,

                'berkala_2' => $berkala2
                    ? $this->formatPemeriksaan($berkala2)
                    : null,

                'status_keseluruhan' => $statusKeseluruhan,

                'kondisi_b1' => $berkala1?->kondisi_umum,
                'kondisi_b2' => $berkala2?->kondisi_umum,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $statistik = [
            'total_siswa' => $dataSiswa->count(),

            'berkala_1_selesai' => $dataSiswa
                ->filter(
                    fn ($item) =>
                        $item['berkala_1'] &&
                        $item['berkala_1']['status'] === 'selesai'
                )
                ->count(),

            'berkala_1_belum' => $dataSiswa
                ->filter(
                    fn ($item) =>
                        !$item['berkala_1'] ||
                        $item['berkala_1']['status'] !== 'selesai'
                )
                ->count(),

            'berkala_2_selesai' => $dataSiswa
                ->filter(
                    fn ($item) =>
                        $item['berkala_2'] &&
                        $item['berkala_2']['status'] === 'selesai'
                )
                ->count(),

            'berkala_2_belum' => $dataSiswa
                ->filter(
                    fn ($item) =>
                        !$item['berkala_2'] ||
                        $item['berkala_2']['status'] !== 'selesai'
                )
                ->count(),

            'lengkap' => $dataSiswa
                ->where('status_keseluruhan', 'lengkap')
                ->count(),

            'belum_lengkap' => $dataSiswa
                ->where('status_keseluruhan', 'belum_lengkap')
                ->count(),

            'belum_diperiksa' => $dataSiswa
                ->where('status_keseluruhan', 'belum_diperiksa')
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | KELAS
        |--------------------------------------------------------------------------
        */

        $kelas = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function ($kelas) {
                return [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'tingkat' => $kelas->tingkat,

                    'jurusan' => $kelas->jurusan
                        ? [
                            'id' => $kelas->jurusan->id,
                            'nama_jurusan' =>
                                $kelas->jurusan->nama_jurusan,
                        ]
                        : null,
                ];
            })
            ->values();

        return Inertia::render(
            'Klinik/Kesehatan/Report/Berkala',
            [
                'periode' => [
                    'id' => $periode->id,
                    'nama_periode' => $periode->nama_periode,
                    'tanggal_mulai' => $periode->tanggal_mulai,
                    'tanggal_selesai' => $periode->tanggal_selesai,
                    'status' => $periode->status,
                ],

                'periodeList' => $periodeList,

                'siswa' => $dataSiswa,

                'statistik' => $statistik,

                'kelas' => $kelas,

                'filter' => [
                    'kelas_id' => $kelasId,
                ],
            ]
        );
    }

    /**
     * ============================================================
     * FORMAT PEMERIKSAAN
     * ============================================================
     */
    private function formatPemeriksaan($item)
    {
        return [
            'id' => $item->id,

            'jenis_pemeriksaan' => $item->jenis_pemeriksaan,

            'tanggal_pemeriksaan' => $item->tanggal_pemeriksaan,

            'status' => $item->status,

            'berat_badan' => $item->berat_badan,

            'tinggi_badan' => $item->tinggi_badan,

            'imt' => $item->imt,

            'tekanan_darah' => $item->tekanan_darah,

            'denyut_nadi' => $item->denyut_nadi,

            'suhu_tubuh' => $item->suhu_tubuh,

            'mata' => $item->mata,

            'telinga' => $item->telinga,

            'gigi_mulut' => $item->gigi_mulut,

            'kondisi_umum' => $item->kondisi_umum,

            'keluhan' => $item->keluhan,

            'hasil_pemeriksaan' => $item->hasil_pemeriksaan,

            'rekomendasi' => $item->rekomendasi,

            'catatan' => $item->catatan,

            'pemeriksa' => $item->pemeriksa
                ? [
                    'id' => $item->pemeriksa->id,
                    'name' => $item->pemeriksa->name,
                ]
                : null,
        ];
    }

    /**
     * ============================================================
     * DOWNLOAD DETAIL PEMERIKSAAN BERKALA
     * ============================================================
     */
    public function downloadDetailPdf(
        PemeriksaanBerkala $pemeriksaan
    ) {
        $pemeriksaan->load([
            'siswa.kelas.jurusan',
            'pemeriksa',
            'periode',
        ]);

        $pdf = Pdf::loadView(
            'klinik.report.berkala-detail',
            [
                'pemeriksaan' => $pemeriksaan,
            ]
        );

        $namaSiswa = str_replace(
            ' ',
            '-',
            strtolower(
                $pemeriksaan->siswa?->nama ?? 'siswa'
            )
        );

        return $pdf->download(
            'pemeriksaan-' .
            $pemeriksaan->jenis_pemeriksaan .
            '-' .
            $namaSiswa .
            '.pdf'
        );
    }

    /**
     * ============================================================
     * DOWNLOAD REKAP PEMERIKSAAN BERKALA - EXCEL
     * ============================================================
     */
    public function downloadExcel(Request $request)
    {
        $periodeId = $request->input('periode_id');

        $periode = $periodeId
            ? Periode::findOrFail($periodeId)
            : Periode::where('status', 'aktif')->first();

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

        $namaPeriode = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $periode->nama_periode
        );

        $namaPeriode = preg_replace(
            '/\s+/',
            '-',
            trim($namaPeriode)
        );

        $namaPeriode = strtolower($namaPeriode);

        return Excel::download(
            new ReportBerkalaExport(
                $periode,
                $request->input('kelas_id')
            ),
            'rekap-pemeriksaan-berkala-' .
                $namaPeriode .
                '.xlsx'
        );
    }

    /**
     * ============================================================
     * DOWNLOAD REKAP PEMERIKSAAN BERKALA - PDF
     * ============================================================
     */
    public function downloadPdf(Request $request)
    {
        $periodeId = $request->input('periode_id');

        $periode = $periodeId
            ? Periode::findOrFail($periodeId)
            : Periode::where('status', 'aktif')->first();

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

        $querySiswa = $periode->siswa()
            ->with([
                'kelas.jurusan',

                'pemeriksaanBerkala' => function ($query) use ($periode) {
                    $query
                        ->where('periode_id', $periode->id)
                        ->whereIn(
                            'jenis_pemeriksaan',
                            ['berkala_1', 'berkala_2']
                        );
                },
            ]);

        if ($request->filled('kelas_id')) {
            $querySiswa->where(
                'kelas_id',
                $request->kelas_id
            );
        }

        $siswas = $querySiswa
            ->orderBy('nama')
            ->get();

        $data = $siswas->map(function ($siswa) {

            $b1 = $siswa->pemeriksaanBerkala
                ->where('jenis_pemeriksaan', 'berkala_1')
                ->sortByDesc('id')
                ->first();

            $b2 = $siswa->pemeriksaanBerkala
                ->where('jenis_pemeriksaan', 'berkala_2')
                ->sortByDesc('id')
                ->first();

            if (
                $b1?->status === 'selesai' &&
                $b2?->status === 'selesai'
            ) {
                $status = 'Lengkap';
            } elseif ($b1 || $b2) {
                $status = 'Belum Lengkap';
            } else {
                $status = 'Belum Diperiksa';
            }

            return [
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas?->nama_kelas ?? '-',

                'b1_status' => $b1?->status ?? 'belum',
                'b1_kondisi' => $b1?->kondisi_umum ?? '-',

                'b2_status' => $b2?->status ?? 'belum',
                'b2_kondisi' => $b2?->kondisi_umum ?? '-',

                'status' => $status,
            ];
        });

        $pdf = Pdf::loadView(
            'klinik.report.berkala-rekap',
            [
                'periode' => $periode,
                'data' => $data,
            ]
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-pemeriksaan-berkala.pdf'
        );
    }

    /**
     * ============================================================
     * REPORT KUNJUNGAN KLINIK
     * ============================================================
     */
    public function kunjungan(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | PERIODE
        |--------------------------------------------------------------------------
        */

        $periodeId = $request->input('periode_id');

        $periode = $periodeId
            ? Periode::find($periodeId)
            : Periode::where('status', 'aktif')->first();

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE
        |--------------------------------------------------------------------------
        */

        $periodeList = Periode::query()
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_periode' => $item->nama_periode,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'status' => $item->status,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA PERIODE
        |--------------------------------------------------------------------------
        */

        if (!$periode) {
            return Inertia::render(
                'Klinik/Kesehatan/Report/Kunjungan',
                [
                    'periode' => null,
                    'periodeList' => $periodeList,
                    'siswa' => [],
                    'statistik' => [
                        'total_kunjungan' => 0,
                        'total_siswa' => 0,
                        'selesai' => 0,
                        'rujuk' => 0,
                    ],
                    'kelas' => [],
                    'filter' => [
                        'kelas_id' =>
                            $request->input('kelas_id'),
                    ],
                ]
            );
        }

        $kelasId = $request->input('kelas_id');

        /*
        |--------------------------------------------------------------------------
        | QUERY KUNJUNGAN
        |--------------------------------------------------------------------------
        |
        | Report ini memang mengambil data dari KunjunganKlinik.
        | Jadi hanya siswa yang mempunyai kunjungan pada periode
        | tersebut yang ditampilkan.
        |
        */

        $query = KunjunganKlinik::query()
            ->with([
                'siswa.kelas.jurusan',
                'pemeriksa',
                'kunjunganObat.obat',
            ])
            ->where(
                'periode_id',
                $periode->id
            );

        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS
        |--------------------------------------------------------------------------
        */

        if ($kelasId) {
            $query->whereHas(
                'siswa',
                function ($q) use ($kelasId) {
                    $q->where(
                        'kelas_id',
                        $kelasId
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $kunjungan = $query
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $statistik = [
            'total_kunjungan' =>
                $kunjungan->count(),

            'total_siswa' =>
                $kunjungan
                    ->pluck('siswa_id')
                    ->filter()
                    ->unique()
                    ->count(),

            'selesai' =>
                $kunjungan
                    ->where('status', 'selesai')
                    ->count(),

            'rujuk' =>
                $kunjungan
                    ->where('status', 'rujuk')
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | DAFTAR KELAS
        |--------------------------------------------------------------------------
        */

        $kelas = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_kelas' => $item->nama_kelas,
                    'tingkat' => $item->tingkat,

                    'jurusan' => $item->jurusan
                        ? [
                            'id' => $item->jurusan->id,
                            'nama_jurusan' =>
                                $item->jurusan->nama_jurusan,
                        ]
                        : null,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA UNTUK VUE
        |--------------------------------------------------------------------------
        */

        $dataKunjungan = $kunjungan
            ->map(function ($item) {

                return [
                    'id' => $item->id,

                    'siswa_id' => $item->siswa_id,

                    'periode_id' => $item->periode_id,

                    'tanggal_kunjungan' =>
                        $item->tanggal_kunjungan,

                    'keluhan' =>
                        $item->keluhan,

                    'pemeriksaan' =>
                        $item->pemeriksaan,

                    'diagnosis' =>
                        $item->diagnosis,

                    'tindakan' =>
                        $item->tindakan,

                    'status' =>
                        $item->status,

                    'catatan' =>
                        $item->catatan,

                    /*
                    |----------------------------------------------------------
                    | SISWA
                    |----------------------------------------------------------
                    */

                    'siswa' => $item->siswa
                        ? [
                            'id' =>
                                $item->siswa->id,

                            'nisn' =>
                                $item->siswa->nisn,

                            'nama' =>
                                $item->siswa->nama,

                            'kelas' =>
                                $item->siswa->kelas
                                    ? [
                                        'id' =>
                                            $item->siswa->kelas->id,

                                        'nama_kelas' =>
                                            $item->siswa->kelas->nama_kelas,
                                    ]
                                    : null,

                            'jurusan' =>
                                $item->siswa->kelas?->jurusan
                                    ? [
                                        'id' =>
                                            $item->siswa->kelas->jurusan->id,

                                        'nama_jurusan' =>
                                            $item->siswa
                                                ->kelas
                                                ->jurusan
                                                ->nama_jurusan,
                                    ]
                                    : null,
                        ]
                        : null,

                    /*
                    |----------------------------------------------------------
                    | PEMERIKSA
                    |----------------------------------------------------------
                    */

                    'pemeriksa' =>
                        $item->pemeriksa
                            ? [
                                'id' =>
                                    $item->pemeriksa->id,

                                'name' =>
                                    $item->pemeriksa->name,

                                'nama' =>
                                    $item->pemeriksa->name,
                            ]
                            : null,

                    /*
                    |----------------------------------------------------------
                    | OBAT
                    |----------------------------------------------------------
                    */

                    'kunjunganObat' =>
                        $item->kunjunganObat
                            ->map(function ($obat) {

                                return [
                                    'id' =>
                                        $obat->id,

                                    'jumlah' =>
                                        $obat->jumlah,

                                    'keterangan' =>
                                        $obat->keterangan,

                                    'obat' =>
                                        $obat->obat
                                            ? [
                                                'id' =>
                                                    $obat->obat->id,

                                                'nama_obat' =>
                                                    $obat->obat->nama_obat,

                                                'satuan' =>
                                                    $obat->obat->satuan,
                                            ]
                                            : null,
                                ];
                            })
                            ->values(),
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RETURN VUE
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Klinik/Kesehatan/Report/Kunjungan',
            [
                'periode' => [
                    'id' =>
                        $periode->id,

                    'nama_periode' =>
                        $periode->nama_periode,

                    'tanggal_mulai' =>
                        $periode->tanggal_mulai,

                    'tanggal_selesai' =>
                        $periode->tanggal_selesai,

                    'status' =>
                        $periode->status,
                ],

                'periodeList' =>
                    $periodeList,

                'siswa' =>
                    $dataKunjungan,

                'statistik' =>
                    $statistik,

                'kelas' =>
                    $kelas,

                'filter' => [
                    'kelas_id' =>
                        $kelasId,
                ],
            ]
        );
    }

    /**
     * ============================================================
     * DOWNLOAD REPORT KUNJUNGAN - EXCEL
     * ============================================================
     */
    public function downloadKunjunganExcel(
        Request $request
    ) {
        $periodeId =
            $request->input('periode_id');

        $periode = $periodeId
            ? Periode::findOrFail($periodeId)
            : Periode::where(
                'status',
                'aktif'
            )->first();

        if (!$periode) {
            abort(
                404,
                'Tidak ada periode aktif.'
            );
        }

        $namaPeriode = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $periode->nama_periode
        );

        $namaPeriode = preg_replace(
            '/\s+/',
            '-',
            trim($namaPeriode)
        );

        $namaPeriode =
            strtolower($namaPeriode);

        $namaFile =
            'rekap-kunjungan-klinik-' .
            $namaPeriode .
            '.xlsx';

        return Excel::download(
            new ReportKunjunganExport(
                $periode,
                $request->input('kelas_id')
            ),
            $namaFile
        );
    }

    /**
     * ============================================================
     * DOWNLOAD REPORT KUNJUNGAN - PDF
     * ============================================================
     */
    public function downloadKunjunganPdf(
        Request $request
    ) {
        $periodeId =
            $request->input('periode_id');

        $periode = $periodeId
            ? Periode::findOrFail($periodeId)
            : Periode::where(
                'status',
                'aktif'
            )->first();

        if (!$periode) {
            abort(
                404,
                'Tidak ada periode aktif.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SISWA DALAM PERIODE
        |--------------------------------------------------------------------------
        */

        $query = $periode->siswa()
            ->with([
                'kelas.jurusan',

                'kunjunganKlinik' => function ($query) use ($periode) {
                    $query
                        ->where(
                            'periode_id',
                            $periode->id
                        )
                        ->with([
                            'pemeriksa',
                            'kunjunganObat.obat',
                        ])
                        ->orderByDesc(
                            'tanggal_kunjungan'
                        );
                },
            ]);

        if ($request->filled('kelas_id')) {
            $query->where(
                'kelas_id',
                $request->kelas_id
            );
        }

        $siswas = $query
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FLATTEN KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $data = $siswas
            ->flatMap(function ($siswa) {

                return $siswa->kunjunganKlinik
                    ->map(function ($kunjungan) use ($siswa) {

                        $obat =
                            $kunjungan
                                ->kunjunganObat
                                ->map(function ($item) {

                                    return
                                        ($item->obat?->nama_obat ?? '-')
                                        . ' (' .
                                        ($item->jumlah ?? 0)
                                        . ')';
                                })
                                ->implode(', ');

                        return [
                            'nama' =>
                                $siswa->nama,

                            'nisn' =>
                                $siswa->nisn ?? '-',

                            'kelas' =>
                                $siswa
                                    ->kelas
                                    ?->nama_kelas ?? '-',

                            'tanggal' =>
                                $kunjungan
                                    ->tanggal_kunjungan,

                            'keluhan' =>
                                $kunjungan
                                    ->keluhan ?? '-',

                            'diagnosis' =>
                                $kunjungan
                                    ->diagnosis ?? '-',

                            'tindakan' =>
                                $kunjungan
                                    ->tindakan ?? '-',

                            'status' =>
                                ucfirst(
                                    $kunjungan
                                        ->status ?? '-'
                                ),

                            'pemeriksa' =>
                                $kunjungan
                                    ->pemeriksa
                                    ?->name ?? '-',

                            'obat' =>
                                $obat ?: '-',
                        ];
                    });
            })
            ->sortByDesc('tanggal')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'klinik.report.kunjungan-rekap',
            [
                'periode' =>
                    $periode,

                'data' =>
                    $data,
            ]
        )->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->download(
            'rekap-kunjungan-klinik.pdf'
        );
    }

    /**
     * ============================================================
     * DOWNLOAD DETAIL KUNJUNGAN - PDF
     * ============================================================
     */
    public function downloadKunjunganDetailPdf(
        KunjunganKlinik $kunjungan
    ) {
        $kunjungan->load([
            'siswa.kelas.jurusan',
            'pemeriksa',
            'periode',
            'kunjunganObat.obat',
        ]);

        $pdf = Pdf::loadView(
            'klinik.report.kunjungan-detail',
            [
                'kunjungan' =>
                    $kunjungan,
            ]
        );

        $namaSiswa = str_replace(
            ' ',
            '-',
            strtolower(
                $kunjungan
                    ->siswa
                    ?->nama ?? 'siswa'
            )
        );

        return $pdf->download(
            'kunjungan-klinik-' .
            $namaSiswa .
            '-' .
            $kunjungan->id .
            '.pdf'
        );
    }
}