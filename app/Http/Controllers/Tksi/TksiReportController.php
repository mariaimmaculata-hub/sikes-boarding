<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Exports\Tksi\TksReportExport;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\TksiHasil;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TksiReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORT TKSI
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $periodeId = $request->periode_id;

        $tingkat = $request->tingkat;

        $jurusanId = $request->jurusan_id;

        $search = trim(
            (string) $request->input('search', '')
        );


        /*
        |--------------------------------------------------------------------------
        | KOMPONEN TKSI
        |--------------------------------------------------------------------------
        */

        $komponenOptions = collect(
            $this->getKomponen()
        );

        $selectedKomponen = $this->getSelectedKomponen(
            $request,
            $komponenOptions
        );


        /*
        |--------------------------------------------------------------------------
        | DATA FILTER DROPDOWN
        |--------------------------------------------------------------------------
        */

        $periodes = Periode::query()
            ->orderByDesc('tanggal_mulai')
            ->get();


        $tingkatOptions = Kelas::query()
            ->whereNotNull('tingkat')
            ->where('tingkat', '!=', '')
            ->distinct()
            ->orderBy('tingkat')
            ->pluck('tingkat')
            ->values();


        $jurusanOptions = Jurusan::query()
            ->orderBy('nama_jurusan')
            ->get([
                'id',
                'nama_jurusan',
            ]);


        /*
        |--------------------------------------------------------------------------
        | DATA REPORT
        |--------------------------------------------------------------------------
        */

        $data = $this->getReportData(
            periodeId: $periodeId,
            komponenOptions: $selectedKomponen,
            tingkat: $tingkat,
            jurusanId: $jurusanId,
            search: $search
        );


        /*
        |--------------------------------------------------------------------------
        | QUERY STATISTIK HASIL
        |--------------------------------------------------------------------------
        */

        $totalHasilQuery = TksiHasil::query();


        if ($periodeId) {
            $totalHasilQuery->where(
                'periode_id',
                $periodeId
            );
        }


        if ($selectedKomponen->isNotEmpty()) {
            $totalHasilQuery->whereIn(
                'komponen',
                $selectedKomponen
            );
        }


        if ($tingkat) {
            $totalHasilQuery->whereHas(
                'siswa.kelas',
                function ($query) use ($tingkat) {
                    $query->where(
                        'tingkat',
                        $tingkat
                    );
                }
            );
        }


        if ($jurusanId) {
            $totalHasilQuery->whereHas(
                'siswa',
                function ($query) use ($jurusanId) {
                    $query->where(
                        'jurusan_id',
                        $jurusanId
                    );
                }
            );
        }


        if ($search !== '') {
            $totalHasilQuery->whereHas(
                'siswa',
                function ($query) use ($search) {

                    $query->where(
                        function ($q) use ($search) {

                            $q->where(
                                'nama',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nisn',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $statistik = [

            'total_siswa' =>
                $data->count(),

            'total_hasil' =>
                $totalHasilQuery->count(),

            'jumlah_komponen' =>
                $selectedKomponen->count(),

            'siswa_lengkap' =>
                $data
                    ->filter(function ($item) use (
                        $selectedKomponen
                    ) {

                        if (
                            $selectedKomponen->isEmpty()
                        ) {

                            return $item[
                                'total_komponen'
                            ] > 0;
                        }

                        return $item[
                            'total_komponen'
                        ]
                        ===
                        $selectedKomponen->count();
                    })
                    ->count(),

            'siswa_belum_lengkap' =>
                $data
                    ->filter(function ($item) use (
                        $selectedKomponen
                    ) {

                        if (
                            $selectedKomponen->isEmpty()
                        ) {

                            return $item[
                                'total_komponen'
                            ] === 0;
                        }

                        return $item[
                            'total_komponen'
                        ]
                        <
                        $selectedKomponen->count();
                    })
                    ->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | RESPONSE INERTIA
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Tksi/Report',
            [

                'data' =>
                    $data,

                'periodes' =>
                    $periodes,

                'komponenOptions' =>
                    $komponenOptions,

                'tingkatOptions' =>
                    $tingkatOptions,

                'jurusanOptions' =>
                    $jurusanOptions,

                'statistik' =>
                    $statistik,

                'filters' => [

                    'periode_id' =>
                        $periodeId,

                    'tingkat' =>
                        $tingkat,

                    'jurusan_id' =>
                        $jurusanId,

                    'search' =>
                        $search,

                    'komponen' =>
                        $request->has('komponen')
                            ? $selectedKomponen
                                ->values()
                                ->all()
                            : [],
                ],
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KOMPONEN TKSI
    |--------------------------------------------------------------------------
    */

    private function getKomponen(): array
    {
        return [

            'hand_eye',

            'vertical_jump',

            't_test',

            'hand_touch',

            'dipping',

            'beep_test',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | KOMPONEN TERPILIH
    |--------------------------------------------------------------------------
    */

    private function getSelectedKomponen(
        Request $request,
        $komponenOptions
    ) {

        if (!$request->has('komponen')) {

            return collect(
                $komponenOptions
            );
        }


        $komponen = $request->input(
            'komponen',
            []
        );


        if (!is_array($komponen)) {

            $komponen = [
                $komponen
            ];
        }


        return collect($komponen)
            ->map(
                fn ($item) =>
                    (string) $item
            )
            ->filter(
                fn ($item) =>
                    collect($komponenOptions)
                        ->contains($item)
            )
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | DATA REPORT
    |--------------------------------------------------------------------------
    */

    private function getReportData(
        $periodeId,
        $komponenOptions,
        $tingkat = null,
        $jurusanId = null,
        $search = ''
    ) {

        /*
        |--------------------------------------------------------------------------
        | QUERY SISWA
        |--------------------------------------------------------------------------
        */

        $siswaQuery = Siswa::query()
            ->with([
                'kelas.jurusan',
            ]);


        /*
        |--------------------------------------------------------------------------
        | FILTER PERIODE
        |--------------------------------------------------------------------------
        */

        if ($periodeId) {

            $siswaQuery->whereHas(
                'periode',
                function ($query) use ($periodeId) {

                    $query->where(
                        'periodes.id',
                        $periodeId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER TINGKAT
        |--------------------------------------------------------------------------
        */

        if ($tingkat) {

            $siswaQuery->whereHas(
                'kelas',
                function ($query) use ($tingkat) {

                    $query->where(
                        'tingkat',
                        $tingkat
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER JURUSAN
        |--------------------------------------------------------------------------
        */

        if ($jurusanId) {

            $siswaQuery->where(
                'jurusan_id',
                $jurusanId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $siswaQuery->where(
                function ($query) use ($search) {

                    $query
                        ->where(
                            'nama',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'nisn',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL SISWA
        |--------------------------------------------------------------------------
        */

        $siswas = $siswaQuery
            ->orderBy('nama')
            ->get();


        if ($siswas->isEmpty()) {

            return collect();
        }


        /*
        |--------------------------------------------------------------------------
        | ID SISWA
        |--------------------------------------------------------------------------
        */

        $siswaIds =
            $siswas->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | QUERY HASIL TKSI
        |--------------------------------------------------------------------------
        */

        $hasilQuery = TksiHasil::query()
            ->whereIn(
                'siswa_id',
                $siswaIds
            );


        /*
        |--------------------------------------------------------------------------
        | FILTER PERIODE
        |--------------------------------------------------------------------------
        */

        if ($periodeId) {

            $hasilQuery->where(
                'periode_id',
                $periodeId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER KOMPONEN
        |--------------------------------------------------------------------------
        */

        if (
            $komponenOptions->isNotEmpty()
        ) {

            $hasilQuery->whereIn(
                'komponen',
                $komponenOptions
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL HASIL
        |--------------------------------------------------------------------------
        */

        $hasil = $hasilQuery
            ->orderByDesc('updated_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GROUP HASIL BERDASARKAN SISWA
        |--------------------------------------------------------------------------
        */

        $hasilBySiswa =
            $hasil->groupBy('siswa_id');


        /*
        |--------------------------------------------------------------------------
        | PERIODE
        |--------------------------------------------------------------------------
        */

        $periode = $periodeId
            ? Periode::find($periodeId)
            : null;


        /*
        |--------------------------------------------------------------------------
        | BENTUK DATA REPORT
        |--------------------------------------------------------------------------
        */

        return $siswas
            ->map(
                function ($siswa) use (
                    $hasilBySiswa,
                    $komponenOptions,
                    $periode
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | HASIL SISWA
                    |--------------------------------------------------------------------------
                    */

                    $items =
                        $hasilBySiswa->get(
                            $siswa->id,
                            collect()
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | DATA KOMPONEN
                    |--------------------------------------------------------------------------
                    */

                    $komponen = [];


                    foreach (
                        $komponenOptions
                        as $namaKomponen
                    ) {

                        $hasilKomponen =
                            $items
                                ->where(
                                    'komponen',
                                    $namaKomponen
                                )
                                ->sortByDesc(
                                    'updated_at'
                                )
                                ->first();


                        if ($hasilKomponen) {

                            /*
                            |--------------------------------------------------------------------------
                            | KATEGORI
                            |--------------------------------------------------------------------------
                            */

                            $kategori =
                                $this->kategoriKomponen(
                                    $hasilKomponen->komponen,
                                    $hasilKomponen->nilai,
                                    $siswa->jenis_kelamin,
                                    $hasilKomponen->level,
                                    $hasilKomponen->balikan
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | DATA KOMPONEN
                            |--------------------------------------------------------------------------
                            */

                            $komponen[
                                $namaKomponen
                            ] = [

                                'id' =>
                                    $hasilKomponen->id,

                                'nilai' =>
                                    $hasilKomponen->nilai,

                                'level' =>
                                    $hasilKomponen->level,

                                'balikan' =>
                                    $hasilKomponen->balikan,

                                'skor' =>
                                    $kategori[
                                        'skor'
                                    ] ?? null,

                                'kategori' =>
                                    $kategori[
                                        'kategori'
                                    ] ?? null,

                                'catatan' =>
                                    $hasilKomponen->catatan,

                                'tanggal' =>
                                    $hasilKomponen->tanggal
                                        ? $hasilKomponen
                                            ->tanggal
                                            ->format('Y-m-d')
                                        : null,

                                'tanggal_tes' =>
                                    $hasilKomponen->tanggal
                                        ? $hasilKomponen
                                            ->tanggal
                                            ->format('Y-m-d')
                                        : null,
                            ];

                        } else {

                            $komponen[
                                $namaKomponen
                            ] = null;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NILAI NUMERIK
                    |--------------------------------------------------------------------------
                    |
                    | Beep Test tidak dimasukkan ke rata-rata nilai
                    | numerik biasa.
                    |
                    */

                    $nilai =
                        collect($komponen)
                            ->filter()
                            ->filter(
                                function ($item) {

                                    return isset(
                                        $item['nilai']
                                    )
                                    &&
                                    $item['nilai'] !== null
                                    &&
                                    $item['nilai'] !== '';
                                }
                            )
                            ->pluck('nilai')
                            ->map(
                                fn ($value) =>
                                    (float) $value
                            );


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH KOMPONEN
                    |--------------------------------------------------------------------------
                    */

                    $totalKomponen =
                        collect($komponen)
                            ->filter(
                                function ($item) {

                                    return $item !== null;
                                }
                            )
                            ->count();


                    /*
                    |--------------------------------------------------------------------------
                    | SUDAH TES
                    |--------------------------------------------------------------------------
                    */

                    $sudahTes =
                        $this->sudahTes(
                            $komponen,
                            $items
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL DAN WAKTU TES TERBARU
                    |--------------------------------------------------------------------------
                    */

                    $hasilTerbaru = $items
                        ->sortByDesc('updated_at')
                        ->first();

                    $tanggalTes = null;

                    if ($hasilTerbaru) {

                        $tanggalTes =
                            $hasilTerbaru->tanggal
                                ? $hasilTerbaru
                                    ->tanggal
                                    ->setTime(
                                        $hasilTerbaru
                                            ->updated_at
                                            ->hour,
                                        $hasilTerbaru
                                            ->updated_at
                                            ->minute,
                                        $hasilTerbaru
                                            ->updated_at
                                            ->second
                                    )
                                : $hasilTerbaru
                                    ->updated_at;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NILAI AKHIR TKSI
                    |--------------------------------------------------------------------------
                    */

                    $hasilAkhir =
                        $this->hasilAkhirTKSI(
                            $komponen,
                            $komponenOptions
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SEMUA KOMPONEN LENGKAP
                    |--------------------------------------------------------------------------
                    */

                    $semuaLengkap =
                        $this->semuaKomponenLengkap(
                            $komponen,
                            $komponenOptions
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | DATA KELAS
                    |--------------------------------------------------------------------------
                    */

                    $kelas = null;


                    if ($siswa->kelas) {

                        $kelas = [

                            'id' =>
                                $siswa->kelas->id,

                            'nama_kelas' =>
                                $siswa->kelas
                                    ->nama_kelas,

                            'tingkat' =>
                                $this->ambilTingkat(
                                    $siswa->kelas
                                        ->nama_kelas
                                ),

                            'jurusan' =>
                                $siswa->kelas->jurusan
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
                        ];
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RETURN
                    |--------------------------------------------------------------------------
                    */

                    return [

                        'siswa_id' =>
                            $siswa->id,

                        'siswa' => [

                            'id' =>
                                $siswa->id,

                            'nisn' =>
                                $siswa->nisn,

                            'nama' =>
                                $siswa->nama,

                            'jenis_kelamin' =>
                                $siswa->jenis_kelamin,

                            'kelas' =>
                                $kelas,
                        ],

                        'periode' =>
                            $periode,

                        'tanggal_tes' =>
                            $tanggalTes,

                        'komponen' =>
                            $komponen,

                        /*
                        |--------------------------------------------------------------------------
                        | HASIL PERHITUNGAN BACKEND
                        |--------------------------------------------------------------------------
                        */

                        'sudah_tes' =>
                            $sudahTes,

                        'semua_komponen_lengkap' =>
                            $semuaLengkap,

                        'total_komponen' =>
                            $totalKomponen,

                        'total_nilai' =>
                            $nilai->sum(),

                        'rata_rata' =>
                            $nilai->count()
                                ? round(
                                    $nilai->avg(),
                                    2
                                )
                                : null,

                        /*
                        |--------------------------------------------------------------------------
                        | HASIL AKHIR TKSI
                        |--------------------------------------------------------------------------
                        */

                        'hasil_akhir' =>
                            $hasilAkhir,
                    ];
                }
            )
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | KATEGORI KOMPONEN
    |--------------------------------------------------------------------------
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
        | NORMALISASI JENIS KELAMIN
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
        | HAND AND EYE
        |--------------------------------------------------------------------------
        */

        if ($komponen === 'hand_eye') {

            if (
                $nilai === null ||
                $nilai === ''
            ) {
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

            if (
                $nilai === null ||
                $nilai === ''
            ) {
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

            if (
                $nilai === null ||
                $nilai === ''
            ) {
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
        | HAND TOUCH
        |--------------------------------------------------------------------------
        */

        if ($komponen === 'hand_touch') {

            if (
                $nilai === null ||
                $nilai === ''
            ) {
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

            if (
                $nilai === null ||
                $nilai === ''
            ) {
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
        */

        if ($komponen === 'beep_test') {

            if (
                $level === null ||
                $balikan === null ||
                $level === '' ||
                $balikan === ''
            ) {
                return null;
            }


            $level = (int) $level;
            $balikan = (int) $balikan;


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
            | PUTERA
            |--------------------------------------------------------------------------
            */

            if ($putera) {

                if (
                    $this->beepAtLeast(
                        $level,
                        $balikan,
                        12,
                        3
                    )
                ) {
                    return [
                        'skor' => 5,
                        'kategori' => 'Baik Sekali',
                    ];
                }


                if (
                    $this->beepAtLeast(
                        $level,
                        $balikan,
                        9,
                        10
                    )
                ) {
                    return [
                        'skor' => 4,
                        'kategori' => 'Baik',
                    ];
                }


                if (
                    $this->beepAtLeast(
                        $level,
                        $balikan,
                        7,
                        4
                    )
                ) {
                    return [
                        'skor' => 3,
                        'kategori' => 'Sedang',
                    ];
                }


                if (
                    $this->beepAtLeast(
                        $level,
                        $balikan,
                        4,
                        8
                    )
                ) {
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
            | PUTERI
            |--------------------------------------------------------------------------
            */

            if (
                $this->beepAtLeast(
                    $level,
                    $balikan,
                    7,
                    10
                )
            ) {
                return [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                ];
            }


            if (
                $this->beepAtLeast(
                    $level,
                    $balikan,
                    6,
                    2
                )
            ) {
                return [
                    'skor' => 4,
                    'kategori' => 'Baik',
                ];
            }


            if (
                $this->beepAtLeast(
                    $level,
                    $balikan,
                    4,
                    6
                )
            ) {
                return [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                ];
            }


            if (
                $this->beepAtLeast(
                    $level,
                    $balikan,
                    1,
                    5
                )
            ) {
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


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | BANDINGKAN LEVEL + BALIKAN
    |--------------------------------------------------------------------------
    */

    private function beepAtLeast(
        int $level,
        int $balikan,
        int $targetLevel,
        int $targetBalikan
    ): bool {

        if ($level > $targetLevel) {
            return true;
        }

        if ($level < $targetLevel) {
            return false;
        }

        return $balikan >= $targetBalikan;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK BEEP TEST
    |--------------------------------------------------------------------------
    */

    private function isBeepTest(
        string $namaKomponen
    ): bool {

        return preg_replace(
            '/[^a-z0-9]/',
            '',
            strtolower($namaKomponen)
        ) === 'beeptest';
    }


    /*
    |--------------------------------------------------------------------------
    | CEK NILAI KOMPONEN
    |--------------------------------------------------------------------------
    */

    private function hasNilai(
        array $komponen,
        string $namaKomponen
    ): bool {

        $item =
            $komponen[$namaKomponen] ?? null;


        if (!$item) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | BEEP TEST
        |--------------------------------------------------------------------------
        */

        if (
            $this->isBeepTest(
                $namaKomponen
            )
        ) {

            return
                ($item['level'] ?? null) !== null
                &&
                ($item['balikan'] ?? null) !== null
                &&
                ($item['level'] ?? '') !== ''
                &&
                ($item['balikan'] ?? '') !== '';
        }


        /*
        |--------------------------------------------------------------------------
        | KOMPONEN BIASA
        |--------------------------------------------------------------------------
        */

        return
            isset($item['nilai'])
            &&
            $item['nilai'] !== null
            &&
            $item['nilai'] !== '';
    }


    /*
    |--------------------------------------------------------------------------
    | CEK SUDAH TES
    |--------------------------------------------------------------------------
    */

    private function sudahTes(
        array $komponen,
        $items
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Kalau ada hasil di database
        |--------------------------------------------------------------------------
        */

        if (
            $items instanceof \Illuminate\Support\Collection
            &&
            $items->isNotEmpty()
        ) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback berdasarkan komponen
        |--------------------------------------------------------------------------
        */

        foreach (
            array_keys($komponen)
            as $namaKomponen
        ) {

            if (
                $this->hasNilai(
                    $komponen,
                    $namaKomponen
                )
            ) {
                return true;
            }
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | CEK SEMUA KOMPONEN LENGKAP
    |--------------------------------------------------------------------------
    */

    private function semuaKomponenLengkap(
        array $komponen,
        $komponenOptions
    ): bool {

        if (
            $komponenOptions->isEmpty()
        ) {
            return false;
        }


        foreach (
            $komponenOptions
            as $namaKomponen
        ) {

            if (
                !$this->hasNilai(
                    $komponen,
                    $namaKomponen
                )
            ) {
                return false;
            }
        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | KONVERSI KATEGORI KE SKOR
    |--------------------------------------------------------------------------
    */

    private function skorKategori(
        $kategori
    ): ?int {

        $text =
            strtolower(
                trim(
                    (string) $kategori
                )
            );


        if (
            str_contains(
                $text,
                'sangat baik'
            )
            ||
            str_contains(
                $text,
                'baik sekali'
            )
        ) {
            return 5;
        }


        if (
            $text === 'baik'
            ||
            str_contains(
                $text,
                'baik'
            )
        ) {
            return 4;
        }


        if (
            str_contains(
                $text,
                'cukup'
            )
            ||
            str_contains(
                $text,
                'sedang'
            )
        ) {
            return 3;
        }


        if (
            str_contains(
                $text,
                'kurang sekali'
            )
            ||
            str_contains(
                $text,
                'sangat kurang'
            )
        ) {
            return 1;
        }


        if (
            str_contains(
                $text,
                'kurang'
            )
        ) {
            return 2;
        }


        if (
            str_contains(
                $text,
                'buruk'
            )
            ||
            str_contains(
                $text,
                'sangat buruk'
            )
        ) {
            return 1;
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | SKOR KOMPONEN
    |--------------------------------------------------------------------------
    */

    private function getSkorKomponen(
        array $komponen,
        string $namaKomponen
    ): ?int {

        if (
            !$this->hasNilai(
                $komponen,
                $namaKomponen
            )
        ) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | BEEP TEST
        |--------------------------------------------------------------------------
        */

        if (
            $this->isBeepTest(
                $namaKomponen
            )
        ) {

            return
                $komponen[
                    $namaKomponen
                ]['skor'] ?? null;
        }


        /*
        |--------------------------------------------------------------------------
        | KOMPONEN BIASA
        |--------------------------------------------------------------------------
        */

        $item =
            $komponen[
                $namaKomponen
            ] ?? null;


        if (!$item) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Gunakan skor hasil kategori dari backend
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $item['skor']
            )
            &&
            $item['skor'] !== null
        ) {

            return (int) $item['skor'];
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback dari kategori
        |--------------------------------------------------------------------------
        */

        return $this->skorKategori(
            $item['kategori']
                ?? ''
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HASIL AKHIR TKSI
    |--------------------------------------------------------------------------
    */

    private function hasilAkhirTKSI(
        array $komponen,
        $komponenOptions
    ): ?array {

        $skor = [];


        foreach (
            $komponenOptions
            as $namaKomponen
        ) {

            $nilai =
                $this->getSkorKomponen(
                    $komponen,
                    $namaKomponen
                );


            if (
                $nilai !== null
                &&
                $nilai !== ''
            ) {

                $skor[] =
                    (float) $nilai;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BELUM ADA HASIL
        |--------------------------------------------------------------------------
        */

        if (empty($skor)) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | RATA-RATA SKOR
        |--------------------------------------------------------------------------
        */

        $rata =
            array_sum($skor)
            /
            count($skor);


        /*
        |--------------------------------------------------------------------------
        | KATEGORI AKHIR
        |--------------------------------------------------------------------------
        */

        if ($rata >= 4.5) {

            $kategori =
                'Baik Sekali';

        } elseif ($rata >= 3.5) {

            $kategori =
                'Baik';

        } elseif ($rata >= 2.5) {

            $kategori =
                'Sedang';

        } elseif ($rata >= 1.5) {

            $kategori =
                'Kurang';

        } else {

            $kategori =
                'Kurang Sekali';
        }


        return [

            'skor' =>
                round(
                    $rata,
                    2
                ),

            'kategori' =>
                $kategori,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NORMA BEEP TEST
    |--------------------------------------------------------------------------
    */

    private function normaBeepReport(
        ?string $jenisKelamin
    ): array {

        $gender =
            strtolower(
                trim(
                    (string) $jenisKelamin
                )
            );


        $putera =
            in_array(
                $gender,
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


        if ($putera) {

            return [

                [
                    'skor' => 5,
                    'kategori' => 'Baik Sekali',
                    'range' => '≥ L12 B3',
                ],

                [
                    'skor' => 4,
                    'kategori' => 'Baik',
                    'range' => 'L9 B10 – L12 B2',
                ],

                [
                    'skor' => 3,
                    'kategori' => 'Sedang',
                    'range' => 'L7 B4 – L9 B9',
                ],

                [
                    'skor' => 2,
                    'kategori' => 'Kurang',
                    'range' => 'L4 B8 – L7 B3',
                ],

                [
                    'skor' => 1,
                    'kategori' => 'Kurang Sekali',
                    'range' => '≤ L4 B7',
                ],
            ];
        }


        return [

            [
                'skor' => 5,
                'kategori' => 'Baik Sekali',
                'range' => '≥ L7 B10',
            ],

            [
                'skor' => 4,
                'kategori' => 'Baik',
                'range' => 'L6 B2 – L7 B9',
            ],

            [
                'skor' => 3,
                'kategori' => 'Sedang',
                'range' => 'L4 B6 – L6 B1',
            ],

            [
                'skor' => 2,
                'kategori' => 'Kurang',
                'range' => 'L1 B5 – L4 B5',
            ],

            [
                'skor' => 1,
                'kategori' => 'Kurang Sekali',
                'range' => '≤ L1 B4',
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL TINGKAT
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportExcel(
        Request $request
    ) {

        $komponenOptions =
            collect(
                $this->getKomponen()
            );


        $selectedKomponen =
            $this->getSelectedKomponen(
                $request,
                $komponenOptions
            );


        return Excel::download(

            new TksReportExport(
                $request->periode_id,
                $selectedKomponen,
                $request->tingkat,
                $request->jurusan_id,
                $request->search
            ),

            'report-tksi.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf(
        Request $request
    ) {

        $periodeId =
            $request->periode_id;


        $komponenOptions =
            collect(
                $this->getKomponen()
            );


        $selectedKomponen =
            $this->getSelectedKomponen(
                $request,
                $komponenOptions
            );


        $data =
            $this->getReportData(
                periodeId:
                    $periodeId,

                komponenOptions:
                    $selectedKomponen,

                tingkat:
                    $request->tingkat,

                jurusanId:
                    $request->jurusan_id,

                search:
                    $request->search
            );


        $periode =
            $periodeId
                ? Periode::find(
                    $periodeId
                )
                : null;


        $pdf =
            Pdf::loadView(
                'tksi.report',
                [

                    'data' =>
                        $data,

                    'periode' =>
                        $periode,

                    'komponenOptions' =>
                        $selectedKomponen,

                    'statistik' => [

                        'total_siswa' =>
                            $data->count(),

                        'jumlah_komponen' =>
                            $selectedKomponen->count(),
                    ],
                ]
            )
            ->setPaper(
                'a4',
                'landscape'
            );


        return $pdf->download(
            'report-tksi.pdf'
        );
    }
}
