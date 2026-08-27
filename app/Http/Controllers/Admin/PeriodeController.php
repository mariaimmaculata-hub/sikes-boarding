<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Periode;
use App\Models\PemeriksaanBerkala;
use App\Models\Siswa;
use App\Models\TksiHasil;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    /**
     * ==========================================================
     * DAFTAR PERIODE
     * ==========================================================
     */
public function index()
{
    $periodes = Periode::with('pembuat')
        ->withCount('siswa')
        ->latest()
        ->paginate(10)
        ->withQueryString();

    // Cari periode yang sedang aktif
    $periodeAktif = Periode::where('status', 'aktif')
        ->latest('tanggal_mulai')
        ->first();

    return Inertia::render('Admin/Periode/Index', [
        'periodes' => $periodes,

        // Data periode aktif untuk kebutuhan popup
        'periodeAktif' => $periodeAktif
            ? [
                'id' => $periodeAktif->id,
                'nama_periode' => $periodeAktif->nama_periode,
                'tanggal_mulai' => $periodeAktif->tanggal_mulai,
                'tanggal_selesai' => $periodeAktif->tanggal_selesai,
            ]
            : null,
    ]);
}

    /**
     * ==========================================================
     * FORM TAMBAH PERIODE
     * ==========================================================
     */
    public function create()
    {
        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return Inertia::render('Admin/Periode/Create', [
            'siswas' => $siswas,
        ]);
    }

    /**
     * ==========================================================
     * SIMPAN PERIODE
     * ==========================================================
     */
   /**
 * ==========================================================
 * SIMPAN PERIODE
 * ==========================================================
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'nama_periode' => [
            'required',
            'string',
            'max:100',
        ],

        'tanggal_mulai' => [
            'required',
            'date',
        ],

        'tanggal_selesai' => [
            'required',
            'date',
            'after_or_equal:tanggal_mulai',
        ],

        'status' => [
            'required',
            'in:aktif,selesai,draft',
        ],

        'siswa_ids' => [
            'nullable',
            'array',
        ],

        'siswa_ids.*' => [
            'integer',
            'exists:siswas,id',
        ],
    ]);

    try {

        DB::transaction(function () use (
            $validated,
            $request
        ) {

            /*
            |--------------------------------------------------------------------------
            | CEK JIKA MAU MEMBUAT PERIODE AKTIF
            |--------------------------------------------------------------------------
            */

            if ($validated['status'] === 'aktif') {

                $periodeAktif = Periode::where(
                    'status',
                    'aktif'
                )
                    ->lockForUpdate()
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | JIKA MASIH ADA PERIODE AKTIF
                |--------------------------------------------------------------------------
                */

                if ($periodeAktif) {

                    throw new \RuntimeException(
                        'Masih terdapat periode aktif. Nonaktifkan periode tersebut terlebih dahulu sebelum membuat periode aktif baru.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT PERIODE
            |--------------------------------------------------------------------------
            */

            $periode = Periode::create([

                'nama_periode' =>
                    $validated['nama_periode'],

                'tanggal_mulai' =>
                    $validated['tanggal_mulai'],

                'tanggal_selesai' =>
                    $validated['tanggal_selesai'],

                'status' =>
                    $validated['status'],

                'created_by' =>
                    $request->user()->id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | HUBUNGKAN SISWA
            |--------------------------------------------------------------------------
            */

            $periode->siswa()->sync(
                $validated['siswa_ids'] ?? []
            );
        });

    } catch (\RuntimeException $e) {

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );
    }

    NotificationService::toRoles(
        ['klinik', 'tksi'],
        'Periode Baru',
        "Periode {$validated['nama_periode']} telah dibuat dan dapat digunakan.",
        'info',
        route('admin.periode.index')
    );

    return redirect()
        ->route('admin.periode.index')
        ->with(
            'success',
            'Periode berhasil ditambahkan.'
        );
}

    
//show
    public function show(Periode $periode)
    {
        $periode->load([
            'pembuat',
            'siswa.kelas.jurusan',
        ]);

        $siswaIds =
            $periode->siswa->pluck('id');

        $siswa = $periode->siswa->map(
            function ($siswa) use ($periode) {

                /*
                |--------------------------------------------------------------------------
                | BERKALA 1
                |--------------------------------------------------------------------------
                */
                $berkala1 =
                    PemeriksaanBerkala::where(
                        'periode_id',
                        $periode->id
                    )
                        ->where(
                            'siswa_id',
                            $siswa->id
                        )
                        ->where(
                            'jenis_pemeriksaan',
                            'berkala_1'
                        )
                        ->latest(
                            'tanggal_pemeriksaan'
                        )
                        ->first();

                /*
                |--------------------------------------------------------------------------
                | BERKALA 2
                |--------------------------------------------------------------------------
                */
                $berkala2 =
                    PemeriksaanBerkala::where(
                        'periode_id',
                        $periode->id
                    )
                        ->where(
                            'siswa_id',
                            $siswa->id
                        )
                        ->where(
                            'jenis_pemeriksaan',
                            'berkala_2'
                        )
                        ->latest(
                            'tanggal_pemeriksaan'
                        )
                        ->first();

                /*
                |--------------------------------------------------------------------------
                | TKSI
                |--------------------------------------------------------------------------
                */
                $tksiHasil =
                    TksiHasil::where(
                        'periode_id',
                        $periode->id
                    )
                        ->where(
                            'siswa_id',
                            $siswa->id
                        )
                        ->orderBy(
                            'tanggal'
                        )
                        ->get();

                /*
                |--------------------------------------------------------------------------
                | HITUNG NILAI AKHIR TKSI
                |--------------------------------------------------------------------------
                */
                $nilaiAkhirTksi =
                    $this->hitungNilaiAkhirTksi(
                        $tksiHasil
                    );

                /*
                |--------------------------------------------------------------------------
                | TKSI LENGKAP JIKA 6 KOMPONEN MEMILIKI NILAI
                |--------------------------------------------------------------------------
                */
                $tksiLengkap =
                    $this->jumlahKomponenTksi(
                        $tksiHasil
                    ) >= 6;

                $tksiTanggal =
                    $tksiHasil->first()?->tanggal;

                /*
                |--------------------------------------------------------------------------
                | KUNJUNGAN KLINIK
                |--------------------------------------------------------------------------
                */
                $kunjungan =
                    KunjunganKlinik::where(
                        'periode_id',
                        $periode->id
                    )
                        ->where(
                            'siswa_id',
                            $siswa->id
                        )
                        ->count();

                return [

                    /*
                    |--------------------------------------------------------------------------
                    | SISWA
                    |--------------------------------------------------------------------------
                    */
                    'id' =>
                        $siswa->id,

                    'nama' =>
                        $siswa->nama,

                    'nisn' =>
                        $siswa->nisn,

                    /*
                    |--------------------------------------------------------------------------
                    | KELAS
                    |--------------------------------------------------------------------------
                    */
                    'kelas' =>
                        $siswa->kelas
                            ? [

                                'id' =>
                                    $siswa->kelas->id,

                                'nama_kelas' =>
                                    $siswa->kelas
                                        ->nama_kelas,

                                'tingkat' =>
                                    $siswa->kelas
                                        ->tingkat
                                        ?? null,

                                'jurusan' =>
                                    $siswa->kelas
                                        ->jurusan
                                        ? [

                                            'id' =>
                                                $siswa
                                                    ->kelas
                                                    ->jurusan
                                                    ->id,

                                            'nama_jurusan' =>
                                                $siswa
                                                    ->kelas
                                                    ->jurusan
                                                    ->nama_jurusan,
                                        ]
                                        : null,
                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 1
                    |--------------------------------------------------------------------------
                    */
                    'berkala_1' => [

                        'selesai' =>
                            $berkala1 !== null,

                        'tanggal' =>
                            $this->formatTanggal(
                                $berkala1
                                    ?->tanggal_pemeriksaan
                            ),

                        'kondisi_umum' =>
                            $berkala1
                                ?->kondisi_umum
                                ?? '-',

                        'hasil_pemeriksaan' =>
                            $berkala1
                                ?->hasil_pemeriksaan
                                ?? '-',

                        'rekomendasi' =>
                            $berkala1
                                ?->rekomendasi
                                ?? '-',

                        'catatan' =>
                            $berkala1
                                ?->catatan
                                ?? '-',
                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 2
                    |--------------------------------------------------------------------------
                    */
                    'berkala_2' => [

                        'selesai' =>
                            $berkala2 !== null,

                        'tanggal' =>
                            $this->formatTanggal(
                                $berkala2
                                    ?->tanggal_pemeriksaan
                            ),

                        'kondisi_umum' =>
                            $berkala2
                                ?->kondisi_umum
                                ?? '-',

                        'hasil_pemeriksaan' =>
                            $berkala2
                                ?->hasil_pemeriksaan
                                ?? '-',

                        'rekomendasi' =>
                            $berkala2
                                ?->rekomendasi
                                ?? '-',

                        'catatan' =>
                            $berkala2
                                ?->catatan
                                ?? '-',
                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | TKSI
                    |--------------------------------------------------------------------------
                    */
                    'tksi' => [

                        'selesai' =>
                            $tksiLengkap,

                        'tanggal' =>
                            $this->formatTanggal(
                                $tksiTanggal
                            ),

                        /*
                        | Nilai akhir:
                        | (nilai1 + nilai2 + ... + nilai6) / 6
                        */
                        'nilai_akhir' =>
                            $nilaiAkhirTksi,

                        /*
                        | Jumlah komponen yang memiliki nilai
                        */
                        'jumlah_komponen' =>
                            $this->jumlahKomponenTksi(
                                $tksiHasil
                            ),

                        /*
                        | Semua hasil per komponen
                        */
                        'hasil' =>
                            $tksiHasil
                                ->map(
                                    function ($hasil) {

                                        return [

                                            'id' =>
                                                $hasil->id,

                                            'komponen' =>
                                                $hasil->komponen,

                                            'kategori' =>
                                                $hasil->kategori,

                                            'nilai' =>
                                                $hasil->nilai,

                                            'level' =>
                                                $hasil->level,

                                            'balikan' =>
                                                $hasil->balikan,

                                            'catatan' =>
                                                $hasil->catatan,

                                            'tanggal' =>
                                                $this->formatTanggal(
                                                    $hasil->tanggal
                                                ),
                                        ];
                                    }
                                )
                                ->values(),
                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | KUNJUNGAN
                    |--------------------------------------------------------------------------
                    */
                    'jumlah_kunjungan' =>
                        $kunjungan,
                ];
            }
        )->values();

        /*
        |--------------------------------------------------------------------------
        | JUMLAH BERKALA 1
        |--------------------------------------------------------------------------
        */
        $jumlahBerkala1 =
            PemeriksaanBerkala::where(
                'periode_id',
                $periode->id
            )
                ->whereIn(
                    'siswa_id',
                    $siswaIds
                )
                ->where(
                    'jenis_pemeriksaan',
                    'berkala_1'
                )
                ->distinct()
                ->count(
                    'siswa_id'
                );

        /*
        |--------------------------------------------------------------------------
        | JUMLAH BERKALA 2
        |--------------------------------------------------------------------------
        */
        $jumlahBerkala2 =
            PemeriksaanBerkala::where(
                'periode_id',
                $periode->id
            )
                ->whereIn(
                    'siswa_id',
                    $siswaIds
                )
                ->where(
                    'jenis_pemeriksaan',
                    'berkala_2'
                )
                ->distinct()
                ->count(
                    'siswa_id'
                );

        /*
        |--------------------------------------------------------------------------
        | JUMLAH SISWA SUDAH TKSI
        |--------------------------------------------------------------------------
        */
        $jumlahTksi =
            TksiHasil::where(
                'periode_id',
                $periode->id
            )
                ->whereIn(
                    'siswa_id',
                    $siswaIds
                )
                ->get()
                ->groupBy('siswa_id')
                ->filter(function ($hasil) {

                    return $this->jumlahKomponenTksi(
                        $hasil
                    ) >= 6;
                })
                ->count();

        /*
        |--------------------------------------------------------------------------
        | JUMLAH KUNJUNGAN
        |--------------------------------------------------------------------------
        */
        $jumlahKunjungan =
            KunjunganKlinik::where(
                'periode_id',
                $periode->id
            )
                ->whereIn(
                    'siswa_id',
                    $siswaIds
                )
                ->count();

        return Inertia::render(
            'Admin/Periode/Show',
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

                    'pembuat' =>
                        $periode->pembuat,

                    'siswa' =>
                        $siswa,

                    'jumlah_berkala_1' =>
                        $jumlahBerkala1,

                    'jumlah_berkala_2' =>
                        $jumlahBerkala2,

                    'jumlah_tksi' =>
                        $jumlahTksi,

                    'jumlah_kunjungan' =>
                        $jumlahKunjungan,
                ],
            ]
        );
    }

    /**
     * ==========================================================
     * FORM EDIT PERIODE
     * ==========================================================
     */
    public function edit(Periode $periode)
    {
        $periode->load('siswa');

        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->where(
                'status',
                'aktif'
            )
            ->orderBy(
                'nama'
            )
            ->get();

        return Inertia::render(
            'Admin/Periode/Edit',
            [
                'periode' =>
                    $periode,

                'siswas' =>
                    $siswas,
            ]
        );
    }

    /**
     * ==========================================================
     * UPDATE PERIODE
     * ==========================================================
     */
   /**
 * ==========================================================
 * UPDATE PERIODE
 * ==========================================================
 */
public function update(
    Request $request,
    Periode $periode
) {
    $validated = $request->validate([
        'nama_periode' => [
            'required',
            'string',
            'max:100',
        ],

        'tanggal_mulai' => [
            'required',
            'date',
        ],

        'tanggal_selesai' => [
            'required',
            'date',
            'after_or_equal:tanggal_mulai',
        ],

        'status' => [
            'required',
            'in:aktif,selesai,draft',
        ],

        'siswa_ids' => [
            'nullable',
            'array',
        ],

        'siswa_ids.*' => [
            'integer',
            'exists:siswas,id',
        ],
    ]);

    try {

        DB::transaction(function () use (
            $validated,
            $periode
        ) {

            /*
            |--------------------------------------------------------------------------
            | CEK PERIODE AKTIF
            |--------------------------------------------------------------------------
            */

            if ($validated['status'] === 'aktif') {

                $periodeAktifLain = Periode::where(
                    'status',
                    'aktif'
                )
                    ->where(
                        'id',
                        '!=',
                        $periode->id
                    )
                    ->lockForUpdate()
                    ->first();

                if ($periodeAktifLain) {

                    throw new \RuntimeException(
                        'Masih terdapat periode aktif lain. Nonaktifkan periode tersebut terlebih dahulu.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE PERIODE
            |--------------------------------------------------------------------------
            */

            $periode->update([

                'nama_periode' =>
                    $validated['nama_periode'],

                'tanggal_mulai' =>
                    $validated['tanggal_mulai'],

                'tanggal_selesai' =>
                    $validated['tanggal_selesai'],

                'status' =>
                    $validated['status'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE SISWA
            |--------------------------------------------------------------------------
            */

            $periode->siswa()->sync(
                $validated['siswa_ids'] ?? []
            );
        });

    } catch (\RuntimeException $e) {

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );
    }

    NotificationService::toRoles(
        ['klinik', 'tksi'],
        'Periode Diperbarui',
        "Periode {$validated['nama_periode']} telah diperbarui.",
        'info',
        route('admin.periode.index')
    );

    return redirect()
        ->route('admin.periode.index')
        ->with(
            'success',
            'Periode berhasil diperbarui.'
        );
}

    /**
     * ==========================================================
     * HAPUS PERIODE
     * ==========================================================
     */
    public function destroy(
        Periode $periode
    ) {
        try {

            $deletedPeriode = $periode->nama_periode;

            DB::transaction(
                function () use ($periode) {

                    $periode->siswa()->detach();

                    $periode->delete();
                }
            );

            NotificationService::toRoles(
                ['klinik', 'tksi'],
                'Periode Dihapus',
                "Periode {$deletedPeriode} telah dihapus oleh Admin.",
                'warning',
                route('admin.periode.index')
            );

            return redirect()
                ->route(
                    'admin.periode.index'
                )
                ->with(
                    'success',
                    'Periode berhasil dihapus.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Periode tidak dapat dihapus karena masih digunakan oleh data lain.'
                );
        }
    }

    /**
     * ==========================================================
     * REPORT SEMUA PERIODE
     * ==========================================================
     */
    public function report()
    {
        $periodes = Periode::with([
            'siswa',
        ])
            ->withCount([
                'siswa as jumlah_siswa',
                'kunjunganKlinik as jumlah_kunjungan',
            ])
            ->orderByDesc(
                'tanggal_mulai'
            )
            ->get();

        $reports = $periodes->map(
            function ($periode) {

                $jumlahSiswa =
                    $periode->jumlah_siswa;

                $siswaIds =
                    $periode->siswa
                        ->pluck('id');

                /*
                |--------------------------------------------------------------------------
                | BERKALA 1
                |--------------------------------------------------------------------------
                */
                $b1Selesai =
                    PemeriksaanBerkala::where(
                        'periode_id',
                        $periode->id
                    )
                        ->whereIn(
                            'siswa_id',
                            $siswaIds
                        )
                        ->where(
                            'jenis_pemeriksaan',
                            'berkala_1'
                        )
                        ->distinct()
                        ->count(
                            'siswa_id'
                        );

                /*
                |--------------------------------------------------------------------------
                | BERKALA 2
                |--------------------------------------------------------------------------
                */
                $b2Selesai =
                    PemeriksaanBerkala::where(
                        'periode_id',
                        $periode->id
                    )
                        ->whereIn(
                            'siswa_id',
                            $siswaIds
                        )
                        ->where(
                            'jenis_pemeriksaan',
                            'berkala_2'
                        )
                        ->distinct()
                        ->count(
                            'siswa_id'
                        );

                /*
                |--------------------------------------------------------------------------
                | TKSI
                |--------------------------------------------------------------------------
                */
                $tksiData =
                    TksiHasil::where(
                        'periode_id',
                        $periode->id
                    )
                        ->whereIn(
                            'siswa_id',
                            $siswaIds
                        )
                        ->get()
                        ->groupBy(
                            'siswa_id'
                        );

                $tksiSelesai =
                    $tksiData
                        ->filter(function ($hasil) {

                            return $this->jumlahKomponenTksi(
                                $hasil
                            ) >= 6;
                        })
                        ->count();

                return [

                    'id' =>
                        $periode->id,

                    'nama_periode' =>
                        $periode->nama_periode,

                    'jumlah_siswa' =>
                        $jumlahSiswa,

                    'jumlah_kunjungan' =>
                        $periode->jumlah_kunjungan,

                    'berkala_1' => [

                        'selesai' =>
                            $b1Selesai,

                        'total' =>
                            $jumlahSiswa,

                        'lengkap' =>
                            $jumlahSiswa > 0 &&
                            $b1Selesai >=
                            $jumlahSiswa,
                    ],

                    'berkala_2' => [

                        'selesai' =>
                            $b2Selesai,

                        'total' =>
                            $jumlahSiswa,

                        'lengkap' =>
                            $jumlahSiswa > 0 &&
                            $b2Selesai >=
                            $jumlahSiswa,
                    ],

                    'tksi' => [

                        'selesai' =>
                            $tksiSelesai,

                        'total' =>
                            $jumlahSiswa,

                        'lengkap' =>
                            $jumlahSiswa > 0 &&
                            $tksiSelesai >=
                            $jumlahSiswa,
                    ],
                ];
            }
        );

        return Inertia::render(
            'Admin/Periode/Report',
            [
                'reports' =>
                    $reports,
            ]
        );
    }

    /**
     * ==========================================================
     * DETAIL REPORT PERIODE
     * ==========================================================
     */
    public function showReport(
        Periode $periode
    ) {
        $periode->load([
            'siswa.kelas.jurusan',
        ]);

        $siswaReports =
            $periode->siswa->map(
                function ($siswa) use ($periode) {

                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 1
                    |--------------------------------------------------------------------------
                    */
                    $b1 =
                        PemeriksaanBerkala::where(
                            'periode_id',
                            $periode->id
                        )
                            ->where(
                                'siswa_id',
                                $siswa->id
                            )
                            ->where(
                                'jenis_pemeriksaan',
                                'berkala_1'
                            )
                            ->latest(
                                'tanggal_pemeriksaan'
                            )
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 2
                    |--------------------------------------------------------------------------
                    */
                    $b2 =
                        PemeriksaanBerkala::where(
                            'periode_id',
                            $periode->id
                        )
                            ->where(
                                'siswa_id',
                                $siswa->id
                            )
                            ->where(
                                'jenis_pemeriksaan',
                                'berkala_2'
                            )
                            ->latest(
                                'tanggal_pemeriksaan'
                            )
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | KUNJUNGAN KLINIK
                    |--------------------------------------------------------------------------
                    */
                    $jumlahKunjungan =
                        KunjunganKlinik::where(
                            'periode_id',
                            $periode->id
                        )
                            ->where(
                                'siswa_id',
                                $siswa->id
                            )
                            ->count();

                    /*
                    |--------------------------------------------------------------------------
                    | TKSI
                    |--------------------------------------------------------------------------
                    */
                    $tksiHasil =
                        TksiHasil::where(
                            'periode_id',
                            $periode->id
                        )
                            ->where(
                                'siswa_id',
                                $siswa->id
                            )
                            ->orderBy(
                                'tanggal'
                            )
                            ->get();

                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG NILAI AKHIR
                    |--------------------------------------------------------------------------
                    */
                    $nilaiAkhirTksi =
                        $this->hitungNilaiAkhirTksi(
                            $tksiHasil
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS TKSI
                    |--------------------------------------------------------------------------
                    */
                    $tksiLengkap =
                        $this->jumlahKomponenTksi(
                            $tksiHasil
                        ) >= 6;

                    $tksiTanggal =
                        $tksiHasil
                            ->first()
                            ?->tanggal;

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS KESELURUHAN
                    |--------------------------------------------------------------------------
                    */
                    $b1Lengkap =
                        $b1 !== null;

                    $b2Lengkap =
                        $b2 !== null;

                    $lengkap =
                        $b1Lengkap &&
                        $b2Lengkap &&
                        $tksiLengkap;

                    return [

                        /*
                        |--------------------------------------------------------------------------
                        | IDENTITAS SISWA
                        |--------------------------------------------------------------------------
                        */
                        'id' =>
                            $siswa->id,

                        'nama' =>
                            $siswa->nama,

                        'nisn' =>
                            $siswa->nisn,

                        'kelas' =>
                            $siswa->kelas
                                ?->nama_kelas
                            ?? '-',

                        'jurusan' =>
                            $siswa->kelas
                                ?->jurusan
                                ?->nama_jurusan
                            ?? null,

                        /*
                        |--------------------------------------------------------------------------
                        | KUNJUNGAN
                        |--------------------------------------------------------------------------
                        */
                        'jumlah_kunjungan' =>
                            $jumlahKunjungan,

                        /*
                        |--------------------------------------------------------------------------
                        | BERKALA 1
                        |--------------------------------------------------------------------------
                        */
                        'b1' => [

                            'lengkap' =>
                                $b1Lengkap,

                            'tanggal' =>
                                $this->formatTanggal(
                                    $b1
                                        ?->tanggal_pemeriksaan
                                ),

                            'kondisi_umum' =>
                                $b1
                                    ?->kondisi_umum
                                ?? '-',

                            'hasil_pemeriksaan' =>
                                $b1
                                    ?->hasil_pemeriksaan
                                ?? '-',

                            'rekomendasi' =>
                                $b1
                                    ?->rekomendasi
                                ?? '-',

                            'catatan' =>
                                $b1
                                    ?->catatan
                                ?? '-',
                        ],

                        /*
                        |--------------------------------------------------------------------------
                        | BERKALA 2
                        |--------------------------------------------------------------------------
                        */
                        'b2' => [

                            'lengkap' =>
                                $b2Lengkap,

                            'tanggal' =>
                                $this->formatTanggal(
                                    $b2
                                        ?->tanggal_pemeriksaan
                                ),

                            'kondisi_umum' =>
                                $b2
                                    ?->kondisi_umum
                                ?? '-',

                            'hasil_pemeriksaan' =>
                                $b2
                                    ?->hasil_pemeriksaan
                                ?? '-',

                            'rekomendasi' =>
                                $b2
                                    ?->rekomendasi
                                ?? '-',

                            'catatan' =>
                                $b2
                                    ?->catatan
                                ?? '-',
                        ],

                        /*
                        |--------------------------------------------------------------------------
                        | TKSI
                        |--------------------------------------------------------------------------
                        */
                        'tksi' => [

                            'lengkap' =>
                                $tksiLengkap,

                            'tanggal' =>
                                $this->formatTanggal(
                                    $tksiTanggal
                                ),

                            /*
                            | Nilai akhir:
                            |
                            | (nilai1 + nilai2 + ... + nilai6) / 6
                            */
                            'nilai_akhir' =>
                                $nilaiAkhirTksi,

                            /*
                            | Jumlah komponen
                            */
                            'jumlah_komponen' =>
                                $this->jumlahKomponenTksi(
                                    $tksiHasil
                                ),

                            /*
                            | Semua hasil per komponen
                            */
                            'hasil' =>
                                $tksiHasil
                                    ->map(
                                        function ($hasil) {

                                            return [

                                                'id' =>
                                                    $hasil->id,

                                                'komponen' =>
                                                    $hasil->komponen,

                                                'kategori' =>
                                                    $hasil->kategori,

                                                'nilai' =>
                                                    $hasil->nilai,

                                                'level' =>
                                                    $hasil->level,

                                                'balikan' =>
                                                    $hasil->balikan,

                                                'catatan' =>
                                                    $hasil->catatan,

                                                'tanggal' =>
                                                    $this->formatTanggal(
                                                        $hasil->tanggal
                                                    ),
                                            ];
                                        }
                                    )
                                    ->values(),
                        ],

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS KESELURUHAN
                        |--------------------------------------------------------------------------
                        */
                        'status' =>
                            $lengkap
                                ? 'Lengkap'
                                : 'Belum Lengkap',
                    ];
                }
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */
        $totalSiswa =
            $siswaReports->count();

        $totalKunjungan =
            KunjunganKlinik::where(
                'periode_id',
                $periode->id
            )->count();

        $totalLengkap =
            $siswaReports
                ->where(
                    'status',
                    'Lengkap'
                )
                ->count();

        return Inertia::render(
            'Admin/Periode/ShowReport',
            [

                'periode' => [

                    'id' =>
                        $periode->id,

                    'nama_periode' =>
                        $periode->nama_periode,

                    'tanggal_mulai' =>
                        $this->formatTanggal(
                            $periode->tanggal_mulai,
                            'd F Y'
                        ),

                    'tanggal_selesai' =>
                        $this->formatTanggal(
                            $periode->tanggal_selesai,
                            'd F Y'
                        ),
                ],

                'summary' => [

                    'total_siswa' =>
                        $totalSiswa,

                    'total_kunjungan' =>
                        $totalKunjungan,

                    'total_lengkap' =>
                        $totalLengkap,
                ],

                'siswa' =>
                    $siswaReports,
            ]
        );
    }

    /**
     * ==========================================================
     * HITUNG JUMLAH KOMPONEN TKSI
     * ==========================================================
     *
     * Hanya menghitung data TKSI yang memiliki nilai.
     *
     * Jadi:
     *
     * Komponen 1 -> nilai 80 -> dihitung
     * Komponen 2 -> nilai 75 -> dihitung
     * dst.
     *
     * Data yang nilai-nya kosong tidak dihitung.
     */
    private function jumlahKomponenTksi(
        $hasilTksi
    ) {
        if (
            !$hasilTksi ||
            $hasilTksi->isEmpty()
        ) {
            return 0;
        }

        return $hasilTksi
            ->filter(function ($hasil) {

                return $hasil->nilai !== null
                    && trim(
                        (string) $hasil->nilai
                    ) !== '';
            })
            ->count();
    }

    /**
     * ==========================================================
     * HITUNG NILAI AKHIR TKSI
     * ==========================================================
     *
     * Rumus:
     *
     * (Skor 1 + Skor 2 + Skor 3 +
     *  Skor 4 + Skor 5 + Skor 6) / 6
     *
     * Contoh:
     *
     * 80 + 75 + 70 + 85 + 90 + 80
     * = 480
     *
     * 480 / 6
     * = 80
     */
    private function hitungNilaiAkhirTksi(
        $hasilTksi
    ) {
        if (
            !$hasilTksi ||
            $hasilTksi->isEmpty()
        ) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil hanya komponen yang mempunyai nilai
        |--------------------------------------------------------------------------
        */
        $komponen = $hasilTksi
            ->filter(function ($hasil) {

                return $hasil->nilai !== null
                    && trim(
                        (string) $hasil->nilai
                    ) !== '';
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Harus ada minimal 6 komponen
        |--------------------------------------------------------------------------
        */
        if ($komponen->count() < 6) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil 6 komponen pertama
        |--------------------------------------------------------------------------
        */
        $komponen =
            $komponen->take(6);

        /*
        |--------------------------------------------------------------------------
        | Jumlahkan semua nilai
        |--------------------------------------------------------------------------
        */
        $totalSkor =
            $komponen->sum(
                function ($hasil) {

                    return (float) $hasil->nilai;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Bagi 6
        |--------------------------------------------------------------------------
        */
        $nilaiAkhir =
            $totalSkor / 6;

        /*
        |--------------------------------------------------------------------------
        | 2 angka di belakang koma
        |--------------------------------------------------------------------------
        */
        return round(
            $nilaiAkhir,
            2
        );
    }

    /**
     * ==========================================================
     * FORMAT TANGGAL
     * ==========================================================
     */
    private function formatTanggal(
        $tanggal,
        string $format = 'd M Y'
    ) {
        if (!$tanggal) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | SUDAH CARBON
        |--------------------------------------------------------------------------
        */
        if (
            is_object($tanggal) &&
            method_exists(
                $tanggal,
                'format'
            )
        ) {
            return $tanggal->format(
                $format
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STRING
        |--------------------------------------------------------------------------
        */
        try {

            return \Carbon\Carbon::parse(
                $tanggal
            )->format(
                $format
            );

        } catch (\Throwable $e) {

            return $tanggal;
        }
    }

    /**
 * ==========================================================
 * NONAKTIFKAN PERIODE AKTIF DAN BUAT PERIODE BARU
 * ==========================================================
 */
public function deactivateActiveAndCreate()
{
    $periodeNama = null;

    DB::transaction(function () use (&$periodeNama) {

        $periodeAktif = Periode::where('status', 'aktif')
            ->lockForUpdate()
            ->first();

        if ($periodeAktif) {

            $periodeNama = $periodeAktif->nama_periode;

            $periodeAktif->update([
                'status' => 'selesai',
            ]);
        }
    });

    if ($periodeNama) {
        NotificationService::toRoles(
            ['klinik', 'tksi'],
            'Periode Berakhir',
            "Periode {$periodeNama} telah dinyatakan selesai oleh Admin.",
            'warning',
            route('admin.periode.index')
        );
    }

    return redirect()
        ->route('admin.periode.create')
        ->with(
            'success',
            'Periode aktif sebelumnya telah dinonaktifkan. Silakan buat periode baru.'
        );
}
}