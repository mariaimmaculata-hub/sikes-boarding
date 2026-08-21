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
    /*
    |--------------------------------------------------------------------------
    | REPORT PEMERIKSAAN BERKALA
    |--------------------------------------------------------------------------
    */

    public function berkala(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | PERIODE
        |--------------------------------------------------------------------------
        */

        $periode = $this->getPeriode($request);

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE
        |--------------------------------------------------------------------------
        */

        $periodeList = $this->getPeriodeList();

        /*
        |--------------------------------------------------------------------------
        | JIKA BELUM ADA PERIODE
        |--------------------------------------------------------------------------
        */

        if (!$periode) {
            return Inertia::render(
                'Klinik/Kesehatan/Report/Berkala',
                [
                    'periode' => null,
                    'periodeList' => $periodeList,
                    'siswa' => [],
                    'statistik' => $this->emptyBerkalaStatistik(),
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
                        ->with('pemeriksa')
                        ->orderByDesc('id');
                },
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS
        |--------------------------------------------------------------------------
        */

        if ($kelasId) {
            $querySiswa->where('kelas_id', $kelasId);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $siswas = $querySiswa
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA
        |--------------------------------------------------------------------------
        */

        $dataSiswa = $siswas
            ->map(function ($siswa) {
                $berkala1 = $siswa->pemeriksaanBerkala
                    ->where('jenis_pemeriksaan', 'berkala_1')
                    ->sortByDesc('id')
                    ->first();

                $berkala2 = $siswa->pemeriksaanBerkala
                    ->where('jenis_pemeriksaan', 'berkala_2')
                    ->sortByDesc('id')
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | STATUS KESELURUHAN
                |--------------------------------------------------------------------------
                */

                if (
                    $berkala1?->status === 'selesai' &&
                    $berkala2?->status === 'selesai'
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
            })
            ->values();

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
        | DAFTAR KELAS
        |--------------------------------------------------------------------------
        */

        $kelas = $this->getKelas();

        /*
        |--------------------------------------------------------------------------
        | RETURN INERTIA
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Klinik/Kesehatan/Report/Berkala',
            [
                'periode' => $this->formatPeriode($periode),

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

    /*
    |--------------------------------------------------------------------------
    | FORMAT PEMERIKSAAN BERKALA
    |--------------------------------------------------------------------------
    */

    private function formatPemeriksaan($item)
    {
        return [
            'id' => $item->id,

            'jenis_pemeriksaan' => $item->jenis_pemeriksaan,

            'tanggal_pemeriksaan' =>
                $item->tanggal_pemeriksaan,

            'status' => $item->status,

            'berat_badan' => $item->berat_badan,

            'tinggi_badan' => $item->tinggi_badan,

            'imt' => $item->imt,

            'tekanan_darah' =>
                $item->tekanan_darah,

            'denyut_nadi' =>
                $item->denyut_nadi,

            'suhu_tubuh' =>
                $item->suhu_tubuh,

            'mata' => $item->mata,

            'telinga' => $item->telinga,

            'gigi_mulut' =>
                $item->gigi_mulut,

            'kondisi_umum' =>
                $item->kondisi_umum,

            'keluhan' =>
                $item->keluhan,

            'hasil_pemeriksaan' =>
                $item->hasil_pemeriksaan,

            'rekomendasi' =>
                $item->rekomendasi,

            'catatan' =>
                $item->catatan,

            'pemeriksa' => $item->pemeriksa
                ? [
                    'id' => $item->pemeriksa->id,
                    'name' => $item->pemeriksa->name,
                ]
                : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD DETAIL PEMERIKSAAN BERKALA
    |--------------------------------------------------------------------------
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

        $namaSiswa = $this->sanitizeFileName(
            $pemeriksaan->siswa?->nama ?? 'siswa'
        );

        return $pdf->download(
            'pemeriksaan-' .
                $pemeriksaan->jenis_pemeriksaan .
                '-' .
                $namaSiswa .
                '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REKAP BERKALA - EXCEL
    |--------------------------------------------------------------------------
    */

    public function downloadExcel(Request $request)
    {
        $periode = $this->getPeriode($request);

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

        $namaPeriode = $this->sanitizeFileName(
            $periode->nama_periode
        );

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

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REKAP BERKALA - PDF
    |--------------------------------------------------------------------------
    */

    public function downloadPdf(Request $request)
    {
        $periode = $this->getPeriode($request);

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

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
                        );
                },
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('kelas_id')) {
            $querySiswa->where(
                'kelas_id',
                $request->kelas_id
            );
        }

        $siswas = $querySiswa
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA PDF
        |--------------------------------------------------------------------------
        */

        $data = $siswas
            ->map(function ($siswa) {
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

                    'nisn' =>
                        $siswa->nisn ?? '-',

                    'kelas' =>
                        $siswa->kelas?->nama_kelas ?? '-',

                    'b1_status' =>
                        $b1?->status ?? 'belum',

                    'b1_kondisi' =>
                        $b1?->kondisi_umum ?? '-',

                    'b2_status' =>
                        $b2?->status ?? 'belum',

                    'b2_kondisi' =>
                        $b2?->kondisi_umum ?? '-',

                    'status' => $status,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'klinik.report.berkala-rekap',
            [
                'periode' => $periode,
                'data' => $data,
            ]
        )->setPaper(
            'a4',
            'landscape'
        );

        $namaPeriode = $this->sanitizeFileName(
            $periode->nama_periode
        );

        return $pdf->download(
            'rekap-pemeriksaan-berkala-' .
                $namaPeriode .
                '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REPORT KUNJUNGAN KLINIK
    |--------------------------------------------------------------------------
    */

    public function kunjungan(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | PERIODE
        |--------------------------------------------------------------------------
        */

        $periode = $this->getPeriode($request);

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE
        |--------------------------------------------------------------------------
        */

        $periodeList = $this->getPeriodeList();

        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA PERIODE
        |--------------------------------------------------------------------------
        */

        if (!$periode) {
            return Inertia::render(
                'Klinik/Kesehatan/Report/Kunjungan',
                [
                    'periode' => null,

                    'periodeList' =>
                        $periodeList,

                    'siswa' => [],

                    'statistik' =>
                        $this->emptyKunjunganStatistik(),

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
        | AMBIL DATA
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

        $kelas = $this->getKelas();

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATA UNTUK VUE
        |--------------------------------------------------------------------------
        */

        $dataKunjungan = $kunjungan
            ->map(function ($item) {
                return [
                    'id' =>
                        $item->id,

                    'siswa_id' =>
                        $item->siswa_id,

                    'periode_id' =>
                        $item->periode_id,

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
                    |--------------------------------------------------------------------------
                    | SISWA
                    |--------------------------------------------------------------------------
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
                                            $item->siswa
                                                ->kelas
                                                ->jurusan
                                                ->id,

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
                    |--------------------------------------------------------------------------
                    | PEMERIKSA
                    |--------------------------------------------------------------------------
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
                    |--------------------------------------------------------------------------
                    | OBAT
                    |--------------------------------------------------------------------------
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
                'periode' =>
                    $this->formatPeriode($periode),

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

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REPORT KUNJUNGAN - EXCEL
    |--------------------------------------------------------------------------
    */

    public function downloadKunjunganExcel(
        Request $request
    ) {
        $periode = $this->getPeriode($request);

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

        $namaPeriode = $this->sanitizeFileName(
            $periode->nama_periode
        );

        return Excel::download(
            new ReportKunjunganExport(
                $periode,
                $request->input('kelas_id')
            ),
            'rekap-kunjungan-klinik-' .
                $namaPeriode .
                '.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REPORT KUNJUNGAN - PDF
    |--------------------------------------------------------------------------
    */

    public function downloadKunjunganPdf(
        Request $request
    ) {
        $periode = $this->getPeriode($request);

        if (!$periode) {
            abort(404, 'Tidak ada periode aktif.');
        }

        /*
        |--------------------------------------------------------------------------
        | SISWA DALAM PERIODE
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
                        )
                        ->orderByDesc('id');
                },
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('kelas_id')) {
            $query->where(
                'kelas_id',
                $request->kelas_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SISWA
        |--------------------------------------------------------------------------
        */

        $siswas = $query
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FLATTEN DATA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $data = $siswas
            ->flatMap(function ($siswa) {
                return $siswa->kunjunganKlinik
                    ->map(function ($kunjungan) use ($siswa) {
                        /*
                        |--------------------------------------------------------------------------
                        | OBAT
                        |--------------------------------------------------------------------------
                        */

                        $obat = $kunjungan
                            ->kunjunganObat
                            ->map(function ($item) {
                                return
                                    ($item->obat?->nama_obat ?? '-')
                                    . ' ('
                                    . ($item->jumlah ?? 0)
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
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'klinik.report.kunjungan-rekap',
            [
                'periode' => $periode,
                'data' => $data,
            ]
        )->setPaper(
            'a4',
            'landscape'
        );

        $namaPeriode = $this->sanitizeFileName(
            $periode->nama_periode
        );

        return $pdf->download(
            'rekap-kunjungan-klinik-' .
                $namaPeriode .
                '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD DETAIL KUNJUNGAN - PDF
    |--------------------------------------------------------------------------
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
                'kunjungan' => $kunjungan,
            ]
        );

        $namaSiswa = $this->sanitizeFileName(
            $kunjungan->siswa?->nama ?? 'siswa'
        );

        return $pdf->download(
            'kunjungan-klinik-' .
                $namaSiswa .
                '-' .
                $kunjungan->id .
                '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - AMBIL PERIODE
    |--------------------------------------------------------------------------
    |
    | Jika periode_id dikirim:
    |     gunakan periode tersebut.
    |
    | Jika tidak dikirim:
    |     gunakan periode aktif.
    |
    */

    private function getPeriode(Request $request)
    {
        $periodeId = $request->input('periode_id');

        if ($periodeId) {
            return Periode::find($periodeId);
        }

        return Periode::where(
            'status',
            'aktif'
        )->first();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - DAFTAR PERIODE
    |--------------------------------------------------------------------------
    */

    private function getPeriodeList()
    {
        return Periode::query()
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->map(function ($item) {
                return [
                    'id' =>
                        $item->id,

                    'nama_periode' =>
                        $item->nama_periode,

                    'tanggal_mulai' =>
                        $item->tanggal_mulai,

                    'tanggal_selesai' =>
                        $item->tanggal_selesai,

                    'status' =>
                        $item->status,
                ];
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - FORMAT PERIODE
    |--------------------------------------------------------------------------
    */

    private function formatPeriode($periode)
    {
        return [
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
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - DAFTAR KELAS
    |--------------------------------------------------------------------------
    */

    private function getKelas()
    {
        return Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function ($kelas) {
                return [
                    'id' =>
                        $kelas->id,

                    'nama_kelas' =>
                        $kelas->nama_kelas,

                    'tingkat' =>
                        $kelas->tingkat,

                    'jurusan' =>
                        $kelas->jurusan
                            ? [
                                'id' =>
                                    $kelas->jurusan->id,

                                'nama_jurusan' =>
                                    $kelas
                                        ->jurusan
                                        ->nama_jurusan,
                            ]
                            : null,
                ];
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - STATISTIK BERKALA KOSONG
    |--------------------------------------------------------------------------
    */

    private function emptyBerkalaStatistik()
    {
        return [
            'total_siswa' => 0,

            'berkala_1_selesai' => 0,

            'berkala_1_belum' => 0,

            'berkala_2_selesai' => 0,

            'berkala_2_belum' => 0,

            'lengkap' => 0,

            'belum_lengkap' => 0,

            'belum_diperiksa' => 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - STATISTIK KUNJUNGAN KOSONG
    |--------------------------------------------------------------------------
    */

    private function emptyKunjunganStatistik()
    {
        return [
            'total_kunjungan' => 0,

            'total_siswa' => 0,

            'selesai' => 0,

            'rujuk' => 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - NAMA FILE
    |--------------------------------------------------------------------------
    */

    private function sanitizeFileName($name)
    {
        $name = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $name
        );

        $name = preg_replace(
            '/\s+/',
            '-',
            trim($name)
        );

        return strtolower($name);
    }
}