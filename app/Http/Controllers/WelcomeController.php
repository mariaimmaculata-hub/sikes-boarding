<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\PeriodeSiswa;
use App\Models\PemeriksaanBerkala;
use App\Models\KunjunganKlinik;
use App\Models\TksiHasil;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TANGGAL
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $start30Days = $today->copy()->subDays(29);

        $start7Days = $today->copy()->subDays(6);

        $startPrevious7Days = $today->copy()->subDays(13);

        $endPrevious7Days = $today->copy()->subDays(7);


        /*
        |--------------------------------------------------------------------------
        | PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $periodeAktif = Periode::query()
            ->where('status', 'aktif')
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | TOTAL SISWA
        |--------------------------------------------------------------------------
        */

        if ($periodeAktif) {

            $totalSiswa = PeriodeSiswa::query()
                ->where('periode_id', $periodeAktif->id)
                ->count();

        } else {

            $totalSiswa = Siswa::query()->count();

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL KELAS
        |--------------------------------------------------------------------------
        */

        $totalKelas = Kelas::query()->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $totalKunjungan = KunjunganKlinik::query()
            ->count();


        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN BULAN INI
        |--------------------------------------------------------------------------
        */

        $kunjunganBulanIni = KunjunganKlinik::query()
            ->whereMonth(
                'tanggal_kunjungan',
                $today->month
            )
            ->whereYear(
                'tanggal_kunjungan',
                $today->year
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN 30 HARI
        |--------------------------------------------------------------------------
        */

        $totalKunjungan30Hari = KunjunganKlinik::query()
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    $start30Days->toDateString(),
                    $today->toDateString()
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | RATA-RATA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $rataRataKunjungan = round(
            $totalKunjungan30Hari / 30,
            1
        );


        /*
        |--------------------------------------------------------------------------
        | PUNCAK KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $puncakKunjungan = KunjunganKlinik::query()
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    $start30Days->toDateString(),
                    $today->toDateString()
                ]
            )
            ->select(
                'tanggal_kunjungan',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tanggal_kunjungan')
            ->orderByDesc('total')
            ->value('total') ?? 0;


        /*
        |--------------------------------------------------------------------------
        | TREN 7 HARI
        |--------------------------------------------------------------------------
        */

        $kunjungan7Hari = KunjunganKlinik::query()
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    $start7Days->toDateString(),
                    $today->toDateString()
                ]
            )
            ->count();


        $kunjungan7HariSebelumnya = KunjunganKlinik::query()
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    $startPrevious7Days->toDateString(),
                    $endPrevious7Days->toDateString()
                ]
            )
            ->count();


        if ($kunjungan7HariSebelumnya > 0) {

            $tren7Hari = round(
                (
                    ($kunjungan7Hari - $kunjungan7HariSebelumnya)
                    / $kunjungan7HariSebelumnya
                ) * 100,
                1
            );

        } else {

            $tren7Hari = $kunjungan7Hari > 0
                ? 100
                : 0;

        }


        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN BERKALA
        |--------------------------------------------------------------------------
        |
        | jenis_pemeriksaan:
        | - berkala_1
        | - berkala_2
        |
        */

        $pemeriksaanBerkala = PemeriksaanBerkala::query()
            ->count();


        /*
        |--------------------------------------------------------------------------
        | BERKALA 1
        |--------------------------------------------------------------------------
        */

        $berkala1 = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_1'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | BERKALA 2
        |--------------------------------------------------------------------------
        */

        $berkala2 = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_2'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | BERKALA 1 BULAN INI
        |--------------------------------------------------------------------------
        */

        $berkala1BulanIni = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_1'
            )
            ->whereMonth(
                'tanggal_pemeriksaan',
                $today->month
            )
            ->whereYear(
                'tanggal_pemeriksaan',
                $today->year
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | BERKALA 2 BULAN INI
        |--------------------------------------------------------------------------
        */

        $berkala2BulanIni = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_2'
            )
            ->whereMonth(
                'tanggal_pemeriksaan',
                $today->month
            )
            ->whereYear(
                'tanggal_pemeriksaan',
                $today->year
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN BERKALA BULAN INI
        |--------------------------------------------------------------------------
        */

        $pemeriksaanBerkalaBulanIni =
            $berkala1BulanIni +
            $berkala2BulanIni;


        /*
        |--------------------------------------------------------------------------
        | STATUS BERKALA 1
        |--------------------------------------------------------------------------
        */

        $berkala1Selesai = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_1'
            )
            ->where(
                'status',
                'selesai'
            )
            ->count();


        $berkala1Belum = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_1'
            )
            ->where(
                'status',
                'belum'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | STATUS BERKALA 2
        |--------------------------------------------------------------------------
        */

        $berkala2Selesai = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_2'
            )
            ->where(
                'status',
                'selesai'
            )
            ->count();


        $berkala2Belum = PemeriksaanBerkala::query()
            ->where(
                'jenis_pemeriksaan',
                'berkala_2'
            )
            ->where(
                'status',
                'belum'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TES KEBUGARAN / TKSI
        |--------------------------------------------------------------------------
        |
        | CATATAN:
        |
        | Satu siswa memiliki beberapa komponen TKSI.
        |
        | Contoh:
        |
        | Siswa A -> 6 komponen
        | Siswa B -> 6 komponen
        | Siswa C -> 6 komponen
        |
        | Total record tksi_hasil = 18
        |
        | Tetapi jumlah siswa yang melakukan TKSI = 3.
        |
        | Karena itu kita menggunakan DISTINCT siswa_id.
        |
        */


        /*
        |--------------------------------------------------------------------------
        | TKSI SEMUA DATA
        |--------------------------------------------------------------------------
        */

        $tksiQuery = TksiHasil::query();


        /*
        |--------------------------------------------------------------------------
        | FILTER PERIODE AKTIF
        |--------------------------------------------------------------------------
        |
        | Kalau periode aktif tersedia, hanya data TKSI dari
        | periode tersebut yang dihitung.
        |
        */

        if ($periodeAktif) {

            $tksiQuery->where(
                'periode_id',
                $periodeAktif->id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | JUMLAH SISWA YANG SUDAH MELAKUKAN TKSI
        |--------------------------------------------------------------------------
        |
        | BUKAN:
        |
        | TksiHasil::count()
        |
        | Karena itu menghitung jumlah komponen.
        |
        | Contoh:
        | 3 siswa x 6 komponen = 18
        |
        | YANG BENAR:
        |
        | DISTINCT siswa_id = 3 siswa
        |
        */

        $tesKebugaran = (clone $tksiQuery)
            ->whereNotNull('siswa_id')
            ->distinct()
            ->count('siswa_id');


        /*
        |--------------------------------------------------------------------------
        | TES KEBUGARAN BULAN INI
        |--------------------------------------------------------------------------
        |
        | Tetap menghitung JUMLAH SISWA unik,
        | bukan jumlah komponen.
        |
        */

        $tesKebugaranBulanIni = (clone $tksiQuery)
            ->whereNotNull('siswa_id')
            ->whereMonth(
                'updated_at',
                $today->month
            )
            ->whereYear(
                'updated_at',
                $today->year
            )
            ->distinct()
            ->count('siswa_id');


        /*
        |--------------------------------------------------------------------------
        | GRAFIK KUNJUNGAN 30 HARI
        |--------------------------------------------------------------------------
        */

        $visitData = KunjunganKlinik::query()
            ->select(
                'tanggal_kunjungan',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    $start30Days->toDateString(),
                    $today->toDateString()
                ]
            )
            ->groupBy('tanggal_kunjungan')
            ->orderBy('tanggal_kunjungan')
            ->get()
            ->keyBy(function ($item) {

                return Carbon::parse(
                    $item->tanggal_kunjungan
                )->format('Y-m-d');

            });


        $lineLabels = [];

        $lineData = [];


        for ($i = 29; $i >= 0; $i--) {

            $date = $today->copy()->subDays($i);

            $key = $date->format('Y-m-d');

            $lineLabels[] = $date->format('d M');

            $lineData[] =
                $visitData[$key]->total ?? 0;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS KESEHATAN
        |--------------------------------------------------------------------------
        |
        | Status kesehatan diambil dari:
        |
        | pemeriksaan_berkala.kondisi_umum
        |
        | Mapping:
        |
        | sehat
        |     -> Sehat
        |
        | perlu pemantauan
        |     -> Perlu Pemantauan
        |
        | rujuk
        |     -> Tidak Sehat
        |
        */

        $healthy = 0;

        $attention = 0;

        $notHealthy = 0;


        /*
        |--------------------------------------------------------------------------
        | CEK KOLOM KONDISI UMUM
        |--------------------------------------------------------------------------
        */

        $statusColumn = null;

        if (
            Schema::hasColumn(
                'pemeriksaan_berkala',
                'kondisi_umum'
            )
        ) {

            $statusColumn = 'kondisi_umum';

        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL STATUS KESEHATAN
        |--------------------------------------------------------------------------
        */

        if ($statusColumn) {

            $healthStatus = PemeriksaanBerkala::query()
                ->select(
                    $statusColumn,
                    DB::raw('COUNT(*) as total')
                )
                ->whereNotNull($statusColumn)
                ->where(
                    $statusColumn,
                    '!=',
                    ''
                )
                ->groupBy($statusColumn)
                ->get();


            foreach ($healthStatus as $item) {

                $status = strtolower(
                    trim(
                        (string) $item->{$statusColumn}
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | SEHAT
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $status,
                        [
                            'sehat',
                            'healthy',
                            'normal'
                        ],
                        true
                    )
                ) {

                    $healthy += (int) $item->total;

                }


                /*
                |--------------------------------------------------------------------------
                | PERLU PEMANTAUAN
                |--------------------------------------------------------------------------
                */

                elseif (
                    in_array(
                        $status,
                        [
                            'perlu pemantauan',
                            'perlu perhatian',
                            'perhatian',
                            'attention'
                        ],
                        true
                    )
                ) {

                    $attention += (int) $item->total;

                }


                /*
                |--------------------------------------------------------------------------
                | TIDAK SEHAT
                |--------------------------------------------------------------------------
                */

                elseif (
                    in_array(
                        $status,
                        [
                            'rujuk',
                            'dirujuk',
                            'tidak sehat',
                            'sakit',
                            'refer'
                        ],
                        true
                    )
                ) {

                    $notHealthy += (int) $item->total;

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL STATUS KESEHATAN
        |--------------------------------------------------------------------------
        */

        $totalHealthStatus =
            $healthy +
            $attention +
            $notHealthy;


        /*
        |--------------------------------------------------------------------------
        | PERSENTASE DOUGHNUT
        |--------------------------------------------------------------------------
        */

        if ($totalHealthStatus > 0) {

            $doughnutData = [

                round(
                    ($healthy / $totalHealthStatus) * 100,
                    1
                ),

                round(
                    ($attention / $totalHealthStatus) * 100,
                    1
                ),

                round(
                    ($notHealthy / $totalHealthStatus) * 100,
                    1
                ),

            ];

        } else {

            $doughnutData = [
                0,
                0,
                0
            ];

        }


        /*
        |--------------------------------------------------------------------------
        | DOUGHNUT STATUS KESEHATAN
        |--------------------------------------------------------------------------
        */

        $doughnutChart = [

            'labels' => [
                'Sehat',
                'Perlu Pemantauan',
                'Tidak Sehat'
            ],

            'data' => $doughnutData

        ];


        /*
        |--------------------------------------------------------------------------
        | REKAP KESEHATAN PER KELAS
        |--------------------------------------------------------------------------
        */

        $classes = Kelas::query()
            ->orderBy('nama_kelas')
            ->get();


        $tableData = [];


        foreach ($classes as $kelas) {

            /*
            |--------------------------------------------------------------------------
            | TOTAL SISWA PER KELAS
            |--------------------------------------------------------------------------
            */

            $total = Siswa::query()
                ->where(
                    'kelas_id',
                    $kelas->id
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | ID SISWA
            |--------------------------------------------------------------------------
            */

            $studentIds = Siswa::query()
                ->where(
                    'kelas_id',
                    $kelas->id
                )
                ->pluck('id');


            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            $healthyClass = 0;

            $attentionClass = 0;

            $notHealthyClass = 0;


            /*
            |--------------------------------------------------------------------------
            | STATUS KESEHATAN PER KELAS
            |--------------------------------------------------------------------------
            */

            if (
                $statusColumn &&
                $studentIds->count() > 0
            ) {

                $classStatus = PemeriksaanBerkala::query()
                    ->whereIn(
                        'siswa_id',
                        $studentIds
                    )
                    ->whereNotNull(
                        $statusColumn
                    )
                    ->where(
                        $statusColumn,
                        '!=',
                        ''
                    )
                    ->select(
                        $statusColumn,
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy($statusColumn)
                    ->get();


                foreach ($classStatus as $item) {

                    $status = strtolower(
                        trim(
                            (string) $item->{$statusColumn}
                        )
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SEHAT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        in_array(
                            $status,
                            [
                                'sehat',
                                'healthy',
                                'normal'
                            ],
                            true
                        )
                    ) {

                        $healthyClass +=
                            (int) $item->total;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PERLU PEMANTAUAN
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        in_array(
                            $status,
                            [
                                'perlu pemantauan',
                                'perlu perhatian',
                                'perhatian',
                                'attention'
                            ],
                            true
                        )
                    ) {

                        $attentionClass +=
                            (int) $item->total;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TIDAK SEHAT
                    |--------------------------------------------------------------------------
                    */

                    elseif (
                        in_array(
                            $status,
                            [
                                'rujuk',
                                'dirujuk',
                                'tidak sehat',
                                'sakit',
                                'refer'
                            ],
                            true
                        )
                    ) {

                        $notHealthyClass +=
                            (int) $item->total;

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | SISWA YANG SUDAH DIPERIKSA
            |--------------------------------------------------------------------------
            */

            $examined =
                $healthyClass +
                $attentionClass +
                $notHealthyClass;


            /*
            |--------------------------------------------------------------------------
            | PROGRESS
            |--------------------------------------------------------------------------
            */

            $progress = $total > 0
                ? round(
                    ($examined / $total) * 100,
                    1
                )
                : 0;


            /*
            |--------------------------------------------------------------------------
            | STATUS PROGRESS
            |--------------------------------------------------------------------------
            */

            if ($progress >= 100) {

                $status = 'Selesai';

                $barColor =
                    'bg-emerald-500';

                $badgeClass =
                    'bg-emerald-50 text-emerald-700';

            } elseif ($progress >= 75) {

                $status = 'Hampir Selesai';

                $barColor =
                    'bg-blue-500';

                $badgeClass =
                    'bg-blue-50 text-blue-700';

            } elseif ($progress > 0) {

                $status = 'Berlangsung';

                $barColor =
                    'bg-amber-500';

                $badgeClass =
                    'bg-amber-50 text-amber-700';

            } else {

                $status = 'Belum Ada Data';

                $barColor =
                    'bg-slate-300';

                $badgeClass =
                    'bg-slate-100 text-slate-600';

            }


            /*
            |--------------------------------------------------------------------------
            | DATA TABEL
            |--------------------------------------------------------------------------
            */

            $tableData[] = [

                'class' =>
                    $kelas->nama_kelas,

                'total' =>
                    $total,

                'healthy' =>
                    $healthyClass,

                'attention' =>
                    $attentionClass,

                'notHealthy' =>
                    $notHealthyClass,

                'progress' =>
                    $progress,

                'status' =>
                    $status,

                'barColor' =>
                    $barColor,

                'badgeClass' =>
                    $badgeClass

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | PENYAKIT TERBANYAK
        |--------------------------------------------------------------------------
        */

        $diseaseColumn = null;


        if (
            Schema::hasColumn(
                'kunjungan_klinik',
                'penyakit_id'
            )
        ) {

            $diseaseColumn =
                'penyakit_id';

        }


        $diseases = [];

        $diseaseLabels = [];

        $diseaseData = [];


        if ($diseaseColumn) {

            $diseaseRows = KunjunganKlinik::query()
                ->with('penyakit')
                ->whereNotNull(
                    $diseaseColumn
                )
                ->select(
                    $diseaseColumn,
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy(
                    $diseaseColumn
                )
                ->orderByDesc('total')
                ->limit(8)
                ->get();


            $totalDiseaseCases =
                KunjunganKlinik::query()
                    ->whereNotNull(
                        $diseaseColumn
                    )
                    ->count();


            foreach ($diseaseRows as $row) {

                $name =
                    $row->penyakit?->nama_penyakit
                    ?? 'Tidak diketahui';


                $total =
                    (int) $row->total;


                $percentage =
                    $totalDiseaseCases > 0
                    ? round(
                        ($total / $totalDiseaseCases) * 100,
                        1
                    )
                    : 0;


                $diseases[] = [

                    'nama' =>
                        $name,

                    'total' =>
                        $total,

                    'percentage' =>
                        $percentage

                ];


                $diseaseLabels[] =
                    $name;

                $diseaseData[] =
                    $total;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $stats = [

            /*
            |--------------------------------------------------------------------------
            | SISWA
            |--------------------------------------------------------------------------
            */

            'totalSiswa' =>
                $totalSiswa,

            'totalKelas' =>
                $totalKelas,


            /*
            |--------------------------------------------------------------------------
            | KUNJUNGAN
            |--------------------------------------------------------------------------
            */

            'totalKunjungan' =>
                $totalKunjungan,

            'kunjunganBulanIni' =>
                $kunjunganBulanIni,

            'totalKunjungan30Hari' =>
                $totalKunjungan30Hari,

            'rataRataKunjungan' =>
                $rataRataKunjungan,

            'puncakKunjungan' =>
                $puncakKunjungan,

            'tren7Hari' =>
                $tren7Hari,


            /*
            |--------------------------------------------------------------------------
            | PEMERIKSAAN BERKALA
            |--------------------------------------------------------------------------
            */

            'pemeriksaanBerkala' =>
                $pemeriksaanBerkala,

            'pemeriksaanBerkalaBulanIni' =>
                $pemeriksaanBerkalaBulanIni,


            /*
            |--------------------------------------------------------------------------
            | BERKALA 1
            |--------------------------------------------------------------------------
            */

            'berkala1' =>
                $berkala1,

            'berkala1BulanIni' =>
                $berkala1BulanIni,

            'berkala1Selesai' =>
                $berkala1Selesai,

            'berkala1Belum' =>
                $berkala1Belum,


            /*
            |--------------------------------------------------------------------------
            | BERKALA 2
            |--------------------------------------------------------------------------
            */

            'berkala2' =>
                $berkala2,

            'berkala2BulanIni' =>
                $berkala2BulanIni,

            'berkala2Selesai' =>
                $berkala2Selesai,

            'berkala2Belum' =>
                $berkala2Belum,


            /*
            |--------------------------------------------------------------------------
            | TES KEBUGARAN / TKSI
            |--------------------------------------------------------------------------
            */

            'tesKebugaran' =>
                $tesKebugaran,

            'tesKebugaranBulanIni' =>
                $tesKebugaranBulanIni,


            /*
            |--------------------------------------------------------------------------
            | STATUS KESEHATAN
            |--------------------------------------------------------------------------
            */

            'sehat' =>
                $healthy,

            'perluPerhatian' =>
                $attention,

            'tidakSehat' =>
                $notHealthy,

            'totalStatusKesehatan' =>
                $totalHealthStatus,


            /*
            |--------------------------------------------------------------------------
            | PERIODE
            |--------------------------------------------------------------------------
            */

            'periodeAktif' =>
                $periodeAktif?->nama_periode,

        ];


        /*
        |--------------------------------------------------------------------------
        | RETURN WELCOME
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Welcome',
            [

                'stats' =>
                    $stats,

                'lineChart' => [

                    'labels' =>
                        $lineLabels,

                    'data' =>
                        $lineData

                ],

                'doughnutChart' =>
                    $doughnutChart,

                'diseaseChart' => [

                    'labels' =>
                        $diseaseLabels,

                    'data' =>
                        $diseaseData

                ],

                'tableData' =>
                    $tableData,

                'diseases' =>
                    $diseases

            ]
        );
    }
}