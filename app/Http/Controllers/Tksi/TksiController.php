<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\TksiHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TksiController extends Controller
{
    /**
     * ============================================================
     * DAFTAR SISWA TKSI
     * ============================================================
     */
    public function index()
    {
        $periode = Periode::where('status', 'aktif')->first();

        /*
        |--------------------------------------------------------------------------
        | Tidak ada periode aktif
        |--------------------------------------------------------------------------
        */
        if (!$periode) {
            return Inertia::render('Tksi/Input/Index', [
                'periode' => null,

                'siswa' => [],

                'statistik' => [
                    'total' => 0,
                    'lengkap' => 0,
                    'belum_lengkap' => 0,
                ],

                'komponen' => $this->komponen(),

                'flash' => [
                    'success' => session('success'),
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil siswa periode aktif
        |--------------------------------------------------------------------------
        */
        $dataSiswa = $periode->siswa()
            ->with('kelas.jurusan')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Jumlah komponen TKSI
        |--------------------------------------------------------------------------
        */
        $jumlahKomponen = count($this->komponen());

        /*
        |--------------------------------------------------------------------------
        | Ambil hasil TKSI
        |--------------------------------------------------------------------------
        */
        $hasil = TksiHasil::where('periode_id', $periode->id)
            ->get()
            ->groupBy('siswa_id');

        /*
        |--------------------------------------------------------------------------
        | Bentuk data siswa untuk Vue
        |--------------------------------------------------------------------------
        */
        $siswa = $dataSiswa->map(function ($student) use (
            $hasil,
            $jumlahKomponen
        ) {
            $hasilSiswa = $hasil->get(
                $student->id,
                collect()
            );

            $jumlahHasil = $hasilSiswa->count();

            /*
            |--------------------------------------------------------------------------
            | Data kelas
            |--------------------------------------------------------------------------
            */
            $kelas = null;

            if ($student->kelas) {
                $kelas = [
                    'id' => $student->kelas->id,

                    'nama_kelas' =>
                        $student->kelas->nama_kelas,

                    'tingkat' =>
                        $this->ambilTingkat(
                            $student->kelas->nama_kelas
                        ),

                    'jurusan' => $student->kelas->jurusan
                        ? [
                            'id' =>
                                $student->kelas->jurusan->id,

                            'nama_jurusan' =>
                                $student->kelas->jurusan->nama_jurusan,
                        ]
                        : null,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Hasil TKSI
            |--------------------------------------------------------------------------
            */
            $hasilFormatted = $hasilSiswa->mapWithKeys(
                function ($item) use ($student) {

                    $kategori = $this->kategoriKomponen(
                        $item->komponen,
                        $item->nilai,
                        $student->jenis_kelamin,
                        $item->level,
                        $item->balikan
                    );

                    return [
                        $item->komponen => [
                            'id' => $item->id,

                            'tanggal' => $item->tanggal
                                ? $item->tanggal->format('Y-m-d')
                                : null,

                            'nilai' => $item->nilai,

                            'level' => $item->level,

                            'balikan' => $item->balikan,

                            'catatan' => $item->catatan,

                            'skor' =>
                                $kategori['skor'] ?? null,

                            'kategori' =>
                                $kategori['kategori'] ?? null,
                        ],
                    ];
                }
            );

            return [
                /*
                |--------------------------------------------------------------------------
                | Identitas siswa
                |--------------------------------------------------------------------------
                */
                'id' => $student->id,

                'nisn' => $student->nisn,

                'nama' => $student->nama,

                'jenis_kelamin' =>
                    $student->jenis_kelamin,

                /*
                |--------------------------------------------------------------------------
                | Kelas
                |--------------------------------------------------------------------------
                */
                'kelas' => $kelas,

                /*
                |--------------------------------------------------------------------------
                | Status hasil
                |--------------------------------------------------------------------------
                */
                'jumlah_hasil' => $jumlahHasil,

                'total_komponen' =>
                    $jumlahKomponen,

                'lengkap' =>
                    $jumlahHasil >= $jumlahKomponen,

                /*
                |--------------------------------------------------------------------------
                | Hasil
                |--------------------------------------------------------------------------
                */
                'hasil' => $hasilFormatted,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */
        $statistik = [
            'total' => $siswa->count(),

            'lengkap' => $siswa
                ->where('lengkap', true)
                ->count(),

            'belum_lengkap' => $siswa
                ->where('lengkap', false)
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Kirim ke Vue
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Tksi/Input/Index', [
            'periode' => $periode,

            'siswa' => $siswa,

            'statistik' => $statistik,

            'komponen' => $this->komponen(),

            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }


    /**
     * ============================================================
     * FORM INPUT / EDIT HASIL TKSI
     * ============================================================
     */
    public function create(Siswa $siswa)
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil periode aktif
        |--------------------------------------------------------------------------
        */
        $periode = Periode::where('status', 'aktif')->first();

        /*
        |--------------------------------------------------------------------------
        | Tidak ada periode aktif
        |--------------------------------------------------------------------------
        */
        if (!$periode) {
            return Inertia::render('Tksi/Input/Create', [
                'periode' => null,

                'siswaTerpilih' => null,

                'komponen' => $this->komponen(),

                'hasil' => [],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa terdaftar pada periode aktif
        |--------------------------------------------------------------------------
        */
        $terdaftar = $periode->siswa()
            ->where('siswas.id', $siswa->id)
            ->where('status', 'aktif')
            ->exists();

        if (!$terdaftar) {
            return redirect()
                ->route('tksi.input.index')
                ->withErrors([
                    'siswa' =>
                        'Siswa tidak terdaftar pada periode aktif.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil data siswa
        |--------------------------------------------------------------------------
        */
        $siswa->load('kelas.jurusan');

        /*
        |--------------------------------------------------------------------------
        | Ambil hasil TKSI siswa
        |--------------------------------------------------------------------------
        */
        $hasil = TksiHasil::where(
            'periode_id',
            $periode->id
        )
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->get([
                'id',
                'tanggal',
                'komponen',
                'nilai',
                'level',
                'balikan',
                'catatan',
            ])
            ->mapWithKeys(function ($item) use ($siswa) {

                $kategori = $this->kategoriKomponen(
                    $item->komponen,
                    $item->nilai,
                    $siswa->jenis_kelamin,
                    $item->level,
                    $item->balikan
                );

                return [
                    $item->komponen => [
                        'id' => $item->id,

                        'tanggal' => $item->tanggal
                            ? $item->tanggal->format('Y-m-d')
                            : null,

                        'nilai' => $item->nilai,

                        'level' => $item->level,

                        'balikan' => $item->balikan,

                        'catatan' => $item->catatan,

                        'skor' =>
                            $kategori['skor'] ?? null,

                        'kategori' =>
                            $kategori['kategori'] ?? null,
                    ],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Kirim ke Vue
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Tksi/Input/Create', [
            'periode' => $periode,

            'siswaTerpilih' => $siswa,

            'komponen' => $this->komponen(),

            'hasil' => $hasil,
        ]);
    }


    /**
     * ============================================================
     * SIMPAN HASIL TKSI
     * ============================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil periode aktif
        |--------------------------------------------------------------------------
        */
        $periode = Periode::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->withErrors([
                'periode' =>
                    'Tidak ada periode aktif.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'hasil' => [
                'required',
                'array',
                'min:1',
            ],

            'hasil.*.komponen' => [
                'required',
                'string',
                'max:50',
            ],

            'hasil.*.nilai' => [
                'nullable',
                'numeric',
            ],

            'hasil.*.level' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'hasil.*.balikan' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'hasil.*.catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi khusus setiap komponen
        |--------------------------------------------------------------------------
        */
        foreach ($validated['hasil'] as $item) {

            /*
            |--------------------------------------------------------------------------
            | Beep Test
            |--------------------------------------------------------------------------
            */
            if ($item['komponen'] === 'beep_test') {

                if (
                    empty($item['level']) ||
                    empty($item['balikan'])
                ) {
                    return back()
                        ->withErrors([
                            'hasil' =>
                                'Level dan balikan wajib diisi untuk Beep Test.',
                        ])
                        ->withInput();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Komponen lainnya
            |--------------------------------------------------------------------------
            */
            else {

                if (
                    !isset($item['nilai']) ||
                    $item['nilai'] === '' ||
                    $item['nilai'] === null
                ) {
                    return back()
                        ->withErrors([
                            'hasil' =>
                                'Nilai wajib diisi untuk semua komponen selain Beep Test.',
                        ])
                        ->withInput();
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa terdaftar pada periode aktif
        |--------------------------------------------------------------------------
        */
        $terdaftar = $periode->siswa()
            ->where(
                'siswas.id',
                $validated['siswa_id']
            )
            ->where(
                'status',
                'aktif'
            )
            ->exists();

        if (!$terdaftar) {
            return back()->withErrors([
                'siswa_id' =>
                    'Siswa tidak terdaftar pada periode aktif.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan hasil
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use (
            $validated,
            $periode
        ) {

            foreach ($validated['hasil'] as $item) {

                TksiHasil::updateOrCreate(
                    [
                        'periode_id' =>
                            $periode->id,

                        'siswa_id' =>
                            $validated['siswa_id'],

                        'komponen' =>
                            $item['komponen'],
                    ],

                    [
                        'tanggal' =>
                            $validated['tanggal'],

                        'nilai' =>
                            $item['nilai'] ?? null,

                        'level' =>
                            $item['level'] ?? null,

                        'balikan' =>
                            $item['balikan'] ?? null,

                        'catatan' =>
                            $item['catatan'] ?? null,
                    ]
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Kembali ke index
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('tksi.input.index')
            ->with(
                'success',
                'Hasil TKSI berhasil disimpan.'
            );
    }


    /**
     * ============================================================
     * FORM EDIT
     * ============================================================
     */
    public function edit(TksiHasil $tksiHasil)
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil periode
        |--------------------------------------------------------------------------
        */
        $periode = Periode::find(
            $tksiHasil->periode_id
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil siswa
        |--------------------------------------------------------------------------
        */
        $siswa = Siswa::with('kelas.jurusan')
            ->findOrFail(
                $tksiHasil->siswa_id
            );

        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh hasil siswa
        |--------------------------------------------------------------------------
        */
        $hasil = TksiHasil::where(
            'periode_id',
            $tksiHasil->periode_id
        )
            ->where(
                'siswa_id',
                $tksiHasil->siswa_id
            )
            ->get([
                'id',
                'tanggal',
                'komponen',
                'nilai',
                'level',
                'balikan',
                'catatan',
            ])
            ->mapWithKeys(function ($item) use ($siswa) {

                $kategori = $this->kategoriKomponen(
                    $item->komponen,
                    $item->nilai,
                    $siswa->jenis_kelamin,
                    $item->level,
                    $item->balikan
                );

                return [
                    $item->komponen => [
                        'id' => $item->id,

                        'tanggal' => $item->tanggal
                            ? $item->tanggal->format('Y-m-d')
                            : null,

                        'nilai' => $item->nilai,

                        'level' => $item->level,

                        'balikan' => $item->balikan,

                        'catatan' => $item->catatan,

                        'skor' =>
                            $kategori['skor'] ?? null,

                        'kategori' =>
                            $kategori['kategori'] ?? null,
                    ],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Kirim ke form
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Tksi/Input/Create', [
            'periode' => $periode,

            'siswaTerpilih' => $siswa,

            'komponen' => $this->komponen(),

            'hasil' => $hasil,

            'editMode' => true,

            'tksiHasil' => $tksiHasil,
        ]);
    }


    /**
     * ============================================================
     * UPDATE HASIL TKSI
     * ============================================================
     */
    public function update(
        Request $request,
        TksiHasil $tksiHasil
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'hasil' => [
                'required',
                'array',
                'min:1',
            ],

            'hasil.*.komponen' => [
                'required',
                'string',
                'max:50',
            ],

            'hasil.*.nilai' => [
                'nullable',
                'numeric',
            ],

            'hasil.*.level' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'hasil.*.balikan' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'hasil.*.catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi khusus komponen
        |--------------------------------------------------------------------------
        */
        foreach ($validated['hasil'] as $item) {

            if ($item['komponen'] === 'beep_test') {

                if (
                    empty($item['level']) ||
                    empty($item['balikan'])
                ) {
                    return back()
                        ->withErrors([
                            'hasil' =>
                                'Level dan balikan wajib diisi untuk Beep Test.',
                        ])
                        ->withInput();
                }
            } else {

                if (
                    !isset($item['nilai']) ||
                    $item['nilai'] === '' ||
                    $item['nilai'] === null
                ) {
                    return back()
                        ->withErrors([
                            'hasil' =>
                                'Nilai wajib diisi untuk semua komponen selain Beep Test.',
                        ])
                        ->withInput();
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan record yang diedit benar
        |--------------------------------------------------------------------------
        */
        if (
            (int) $tksiHasil->siswa_id
            !==
            (int) $validated['siswa_id']
        ) {
            return back()->withErrors([
                'siswa_id' =>
                    'Siswa tidak sesuai dengan data TKSI yang diedit.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil periode
        |--------------------------------------------------------------------------
        */
        $periode = Periode::find(
            $tksiHasil->periode_id
        );

        if (!$periode) {
            return back()->withErrors([
                'periode' =>
                    'Periode TKSI tidak ditemukan.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa terdaftar pada periode
        |--------------------------------------------------------------------------
        */
        $terdaftar = $periode->siswa()
            ->where(
                'siswas.id',
                $validated['siswa_id']
            )
            ->where(
                'status',
                'aktif'
            )
            ->exists();

        if (!$terdaftar) {
            return back()->withErrors([
                'siswa_id' =>
                    'Siswa tidak terdaftar pada periode tersebut.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update seluruh komponen
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use (
            $validated,
            $periode
        ) {

            foreach ($validated['hasil'] as $item) {

                TksiHasil::updateOrCreate(
                    [
                        'periode_id' =>
                            $periode->id,

                        'siswa_id' =>
                            $validated['siswa_id'],

                        'komponen' =>
                            $item['komponen'],
                    ],

                    [
                        'tanggal' =>
                            $validated['tanggal'],

                        'nilai' =>
                            $item['nilai'] ?? null,

                        'level' =>
                            $item['level'] ?? null,

                        'balikan' =>
                            $item['balikan'] ?? null,

                        'catatan' =>
                            $item['catatan'] ?? null,
                    ]
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('tksi.input.index')
            ->with(
                'success',
                'Hasil TKSI berhasil diperbarui.'
            );
    }


    /**
     * ============================================================
     * HAPUS HASIL TKSI
     * ============================================================
     */
    public function destroy(TksiHasil $tksiHasil)
    {
        /*
        |--------------------------------------------------------------------------
        | Simpan informasi sebelum dihapus
        |--------------------------------------------------------------------------
        */
        $periodeId =
            $tksiHasil->periode_id;

        $siswaId =
            $tksiHasil->siswa_id;

        /*
        |--------------------------------------------------------------------------
        | Hapus seluruh komponen siswa
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use (
            $periodeId,
            $siswaId
        ) {

            TksiHasil::where(
                'periode_id',
                $periodeId
            )
                ->where(
                    'siswa_id',
                    $siswaId
                )
                ->delete();
        });

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('tksi.input.index')
            ->with(
                'success',
                'Hasil TKSI berhasil dihapus.'
            );
    }


    /**
     * ============================================================
     * LAPORAN TKSI
     * ============================================================
     */
    public function report(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Periode yang dipilih
        |--------------------------------------------------------------------------
        */
        $periodeId =
            $request->query('periode_id');

        $periode = null;

        $siswa = collect();

        /*
        |--------------------------------------------------------------------------
        | Statistik default
        |--------------------------------------------------------------------------
        */
        $statistik = [
            'total' => 0,

            'lengkap' => 0,

            'belum_lengkap' => 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | Daftar periode
        |--------------------------------------------------------------------------
        */
        $periodeList = Periode::orderByDesc(
            'tanggal_mulai'
        )->get();

        /*
        |--------------------------------------------------------------------------
        | Jika periode dipilih
        |--------------------------------------------------------------------------
        */
        if ($periodeId) {

            $periode = Periode::find(
                $periodeId
            );

            if ($periode) {

                /*
                |--------------------------------------------------------------------------
                | Ambil siswa
                |--------------------------------------------------------------------------
                */
                $dataSiswa = $periode->siswa()
                    ->with('kelas.jurusan')
                    ->where('status', 'aktif')
                    ->orderBy('nama')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Jumlah komponen
                |--------------------------------------------------------------------------
                */
                $jumlahKomponen = count(
                    $this->komponen()
                );

                /*
                |--------------------------------------------------------------------------
                | Ambil hasil
                |--------------------------------------------------------------------------
                */
                $hasil = TksiHasil::where(
                    'periode_id',
                    $periode->id
                )
                    ->get()
                    ->groupBy('siswa_id');

                /*
                |--------------------------------------------------------------------------
                | Bentuk data siswa
                |--------------------------------------------------------------------------
                */
                $siswa = $dataSiswa->map(
                    function ($student) use (
                        $hasil,
                        $jumlahKomponen
                    ) {

                        $hasilSiswa = $hasil->get(
                            $student->id,
                            collect()
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Data kelas
                        |--------------------------------------------------------------------------
                        */
                        $kelas = null;

                        if ($student->kelas) {

                            $kelas = [
                                'id' =>
                                    $student->kelas->id,

                                'nama_kelas' =>
                                    $student->kelas->nama_kelas,

                                'tingkat' =>
                                    $this->ambilTingkat(
                                        $student->kelas->nama_kelas
                                    ),

                                'jurusan' =>
                                    $student->kelas->jurusan
                                        ? [
                                            'id' =>
                                                $student->kelas
                                                    ->jurusan
                                                    ->id,

                                            'nama_jurusan' =>
                                                $student->kelas
                                                    ->jurusan
                                                    ->nama_jurusan,
                                        ]
                                        : null,
                            ];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Hasil TKSI
                        |--------------------------------------------------------------------------
                        */
                        $hasilFormatted =
                            $hasilSiswa->mapWithKeys(
                                function ($item) use (
                                    $student
                                ) {

                                    $kategori =
                                        $this->kategoriKomponen(
                                            $item->komponen,
                                            $item->nilai,
                                            $student->jenis_kelamin,
                                            $item->level,
                                            $item->balikan
                                        );

                                    return [
                                        $item->komponen => [
                                            'id' =>
                                                $item->id,

                                            'tanggal' =>
                                                $item->tanggal
                                                    ? $item->tanggal->format(
                                                        'Y-m-d'
                                                    )
                                                    : null,

                                            'nilai' =>
                                                $item->nilai,

                                            'level' =>
                                                $item->level,

                                            'balikan' =>
                                                $item->balikan,

                                            'catatan' =>
                                                $item->catatan,

                                            'skor' =>
                                                $kategori['skor']
                                                    ?? null,

                                            'kategori' =>
                                                $kategori['kategori']
                                                    ?? null,
                                        ],
                                    ];
                                }
                            );

                        return [
                            /*
                            |--------------------------------------------------------------------------
                            | Identitas
                            |--------------------------------------------------------------------------
                            */
                            'id' =>
                                $student->id,

                            'nisn' =>
                                $student->nisn,

                            'nama' =>
                                $student->nama,

                            'jenis_kelamin' =>
                                $student->jenis_kelamin,

                            /*
                            |--------------------------------------------------------------------------
                            | Kelas
                            |--------------------------------------------------------------------------
                            */
                            'kelas' =>
                                $kelas,

                            /*
                            |--------------------------------------------------------------------------
                            | Status
                            |--------------------------------------------------------------------------
                            */
                            'jumlah_hasil' =>
                                $hasilSiswa->count(),

                            'total_komponen' =>
                                $jumlahKomponen,

                            'lengkap' =>
                                $hasilSiswa->count()
                                >= $jumlahKomponen,

                            /*
                            |--------------------------------------------------------------------------
                            | Hasil
                            |--------------------------------------------------------------------------
                            */
                            'hasil' =>
                                $hasilFormatted,
                        ];
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | Statistik
                |--------------------------------------------------------------------------
                */
                $statistik = [
                    'total' =>
                        $siswa->count(),

                    'lengkap' =>
                        $siswa
                            ->where(
                                'lengkap',
                                true
                            )
                            ->count(),

                    'belum_lengkap' =>
                        $siswa
                            ->where(
                                'lengkap',
                                false
                            )
                            ->count(),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Kirim ke Vue Report
        |--------------------------------------------------------------------------
        */
        return Inertia::render(
            'Tksi/Report/Index',
            [
                'periodeList' =>
                    $periodeList,

                'periode' =>
                    $periode,

                'siswa' =>
                    $siswa,

                'komponen' =>
                    $this->komponen(),

                'statistik' =>
                    $statistik,
            ]
        );
    }


    /**
     * ============================================================
     * KOMPONEN TES TKSI
     * ============================================================
     */
    private function komponen(): array
    {
        return [

            [
                'key' =>
                    'hand_eye',

                'nama' =>
                    'Hand and Eye Coordination',

                'satuan' =>
                    'jumlah tangkapan',

                'deskripsi' =>
                    'Jumlah tangkapan bola yang berhasil selama 30 detik.',
            ],

            [
                'key' =>
                    'vertical_jump',

                'nama' =>
                    'Vertical Jump',

                'satuan' =>
                    'cm',

                'deskripsi' =>
                    'Selisih tinggi raihan dan tinggi loncatan.',
            ],

            [
                'key' =>
                    't_test',

                'nama' =>
                    'T-Test',

                'satuan' =>
                    'detik',

                'deskripsi' =>
                    'Waktu terbaik penyelesaian lintasan T-Test.',
            ],

            [
                'key' =>
                    'hand_touch',

                'nama' =>
                    'Hand Touch Reaction',

                'satuan' =>
                    'detik',

                'deskripsi' =>
                    'Waktu terbaik dari aba-aba sampai menyentuh cone.',
            ],

            [
                'key' =>
                    'dipping',

                'nama' =>
                    'Dipping',

                'satuan' =>
                    'jumlah',

                'deskripsi' =>
                    'Jumlah pengulangan gerakan dipping yang benar.',
            ],

            [
                'key' =>
                    'beep_test',

                'nama' =>
                    'Beep Test',

                'satuan' =>
                    'level/balikan',

                'deskripsi' =>
                    'Hasil akhir berdasarkan level dan balikan beep test.',
            ],
        ];
    }


    /**
     * ============================================================
     * KATEGORI HASIL TKSI
     * ============================================================
     */
    private function kategoriKomponen(
        string $komponen,
        $nilai,
        ?string $jenisKelamin = null,
        $level = null,
        $balikan = null
    ): ?array {

        /*
        |--------------------------------------------------------------------------
        | PUTERA / PUTERI
        |--------------------------------------------------------------------------
        */
        $jenisKelaminNormal =
            strtolower(
                trim(
                    (string) $jenisKelamin
                )
            );

        $putera =
            in_array(
                $jenisKelaminNormal,
                [
                    'laki-laki',
                    'laki laki',
                    'putera',
                    'l',
                    'male',
                    'm',
                ],
                true
            );

        /*
        |--------------------------------------------------------------------------
        | HAND AND EYE COORDINATION
        |--------------------------------------------------------------------------
        */
        if ($komponen === 'hand_eye') {

            if ($nilai === null || $nilai === '') {
                return null;
            }

            $nilai = (float) $nilai;

            if ($putera) {

                if ($nilai >= 22) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }

                if ($nilai >= 16) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }

                if ($nilai >= 10) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }

                if ($nilai >= 4) {
                    return [
                        'skor' => 2,
                        'kategori' => 'Kurang',
                    ];
                }

                return [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                ];
            }

            if ($nilai >= 15) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }

            if ($nilai >= 10) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }

            if ($nilai >= 5) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }

            if ($nilai >= 1) {
                return [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                ];
            }

            return [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | DIPPING
        |--------------------------------------------------------------------------
        */
        if ($komponen === 'dipping') {

            if ($nilai === null || $nilai === '') {
                return null;
            }

            $nilai = (float) $nilai;

            if ($putera) {

                if ($nilai >= 25) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }

                if ($nilai >= 22) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }

                if ($nilai >= 19) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }

                if ($nilai >= 16) {
                    return [
                        'skor' => 2,
                        'kategori' => 'Kurang',
                    ];
                }

                return [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                ];
            }

            if ($nilai >= 19) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }

            if ($nilai >= 16) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }

            if ($nilai >= 13) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }

            if ($nilai >= 10) {
                return [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                ];
            }

            return [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | VERTICAL JUMP
        |--------------------------------------------------------------------------
        */
        if ($komponen === 'vertical_jump') {

            if ($nilai === null || $nilai === '') {
                return null;
            }

            $nilai = (float) $nilai;

            if ($putera) {

                if ($nilai >= 63) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }

                if ($nilai >= 59) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }

                if ($nilai >= 35) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }

                if ($nilai >= 20) {
                    return [
                        'skor' => 2,
                        'kategori' => 'Kurang',
                    ];
                }

                return [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                ];
            }

            if ($nilai >= 59) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }

            if ($nilai >= 35) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }

            if ($nilai >= 27) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }

            if ($nilai >= 19) {
                return [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                ];
            }

            return [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | HAND TOUCH REACTION
        |--------------------------------------------------------------------------
        */
        if ($komponen === 'hand_touch') {

            if ($nilai === null || $nilai === '') {
                return null;
            }

            $nilai = (float) $nilai;

            if ($putera) {

                if ($nilai <= 0.80) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }

                if ($nilai <= 1.09) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }

                if ($nilai <= 1.39) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }

                if ($nilai <= 1.69) {
                    return [
                        'skor' => 2,
                        'kategori' => 'Kurang',
                    ];
                }

                return [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                ];
            }

            if ($nilai <= 0.91) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }

            if ($nilai <= 1.21) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }

            if ($nilai <= 1.51) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }

            if ($nilai <= 1.81) {
                return [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                ];
            }

            return [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | T-TEST
        |--------------------------------------------------------------------------
        */
        if ($komponen === 't_test') {

            if ($nilai === null || $nilai === '') {
                return null;
            }

            $nilai = (float) $nilai;

            if ($putera) {

                if ($nilai <= 6.63) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }

                if ($nilai <= 10.15) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }

                if ($nilai <= 14.16) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }

                if ($nilai <= 18.17) {
                    return [
                        'skor' => 2,
                        'kategori' => 'Kurang',
                    ];
                }

                return [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                ];
            }

            if ($nilai <= 7.19) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }

            if ($nilai <= 11.20) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }

            if ($nilai <= 15.19) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }

            if ($nilai <= 19.20) {
                return [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                ];
            }

            return [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | BEEP TEST
        |--------------------------------------------------------------------------
        |
        | Beep Test menggunakan:
        | - level
        | - balikan
        |
        | Bukan nilai biasa.
        |
        | Untuk saat ini belum diberikan tabel norma Beep Test,
        | sehingga skor/kategori dikembalikan null.
        |--------------------------------------------------------------------------
        */
        if ($komponen === 'beep_test') {

    if (
        $level === null ||
        $balikan === null
    ) {
        return null;
    }

    $level = (int) $level;
    $balikan = (int) $balikan;

    /*
    |--------------------------------------------------------------------------
    | PUTERA
    |--------------------------------------------------------------------------
    */

    if ($putera) {

        // Skor 5: >= L12 B3
        if (
            $level > 12 ||
            ($level === 12 && $balikan >= 3)
        ) {
            return [
                'skor' => 5,
                'kategori' => 'Baik Sekali',
            ];
        }

        // Skor 4: L9 B10 - L12 B2
        if (
            $level > 9 ||
            ($level === 9 && $balikan >= 10)
        ) {
            return [
                'skor' => 4,
                'kategori' => 'Baik',
            ];
        }

        // Skor 3: L7 B4 - L9 B9
        if (
            $level > 7 ||
            ($level === 7 && $balikan >= 4)
        ) {
            return [
                'skor' => 3,
                'kategori' => 'Sedang',
            ];
        }

        // Skor 2: L4 B8 - L7 B3
        if (
            $level > 4 ||
            ($level === 4 && $balikan >= 8)
        ) {
            return [
                'skor' => 2,
                'kategori' => 'Kurang',
            ];
        }

        // Skor 1: <= L4 B7
        return [
            'skor' => 1,
            'kategori' => 'Kurang Sekali',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PUTERI
    |--------------------------------------------------------------------------
    */

    // Skor 5: >= L7 B10
    if (
        $level > 7 ||
        ($level === 7 && $balikan >= 10)
    ) {
        return [
            'skor' => 5,
            'kategori' => 'Baik Sekali',
        ];
    }

    // Skor 4: L6 B2 - L7 B9
    if (
        $level > 6 ||
        ($level === 6 && $balikan >= 2)
    ) {
        return [
            'skor' => 4,
            'kategori' => 'Baik',
        ];
    }

    // Skor 3: L4 B6 - L6 B1
    if (
        $level > 4 ||
        ($level === 4 && $balikan >= 6)
    ) {
        return [
            'skor' => 3,
            'kategori' => 'Sedang',
        ];
    }

    // Skor 2: L1 B5 - L4 B5
    if (
        $level > 1 ||
        ($level === 1 && $balikan >= 5)
    ) {
        return [
            'skor' => 2,
            'kategori' => 'Kurang',
        ];
    }

    // Skor 1: <= L1 B4
    return [
        'skor' => 1,
        'kategori' => 'Kurang Sekali',
    ];
}

        return null;
    }


    /**
     * ============================================================
     * AMBIL TINGKAT DARI NAMA KELAS
     * ============================================================
     */
    private function ambilTingkat(
        ?string $namaKelas
    ): ?string {

        if (!$namaKelas) {
            return null;
        }

        if (
            preg_match(
                '/^(XII|XI|X)\b/i',
                trim($namaKelas),
                $matches
            )
        ) {
            return strtoupper(
                $matches[1]
            );
        }

        return null;
    }
}