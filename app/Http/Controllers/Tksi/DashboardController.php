<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\PeriodeSiswa;
use App\Models\TksiHasil;
use App\Models\KunjunganKlinik;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $periode = Periode::where('status', 'aktif')
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | TOTAL SISWA PADA PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $totalSiswa = 0;

        if ($periode) {
            $totalSiswa = PeriodeSiswa::where(
                'periode_id',
                $periode->id
            )->count();
        }


        /*
        |--------------------------------------------------------------------------
        | SISWA YANG SUDAH MELAKUKAN TKSI
        |--------------------------------------------------------------------------
        |
        | Menggunakan TksiHasil.
        |
        | Satu siswa bisa memiliki banyak hasil berdasarkan komponen,
        | sehingga harus DISTINCT berdasarkan siswa_id.
        |
        */

        $siswaSudahTksiIds = collect();

        if ($periode) {
            $siswaSudahTksiIds = TksiHasil::where(
                'periode_id',
                $periode->id
            )
                ->whereNotNull('siswa_id')
                ->pluck('siswa_id')
                ->unique()
                ->values();
        }

        $totalSudahTksi = $siswaSudahTksiIds->count();


        /*
        |--------------------------------------------------------------------------
        | SISWA BELUM TKSI
        |--------------------------------------------------------------------------
        */

        $siswaBelumTksiQuery = Siswa::query()
            ->whereHas('periodeSiswa', function ($query) use ($periode) {

                if ($periode) {
                    $query->where(
                        'periode_id',
                        $periode->id
                    );
                }

            })
            ->whereNotIn(
                'id',
                $siswaSudahTksiIds->toArray()
            );


        $totalBelumTksi = $siswaBelumTksiQuery->count();


        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN KLINIK
        |--------------------------------------------------------------------------
        */

        $totalKunjungan = 0;

        if ($periode) {
            $totalKunjungan = KunjunganKlinik::where(
                'periode_id',
                $periode->id
            )->count();
        }


        /*
        |--------------------------------------------------------------------------
        | PESERTA PERLU PERHATIAN
        |--------------------------------------------------------------------------
        |
        | Prioritas:
        |
        | 1. Siswa yang belum melakukan TKSI
        | 2. Siswa dengan hasil TKSI level 1 / 2
        |
        */

        $attentionStudents = [];


        /*
        |--------------------------------------------------------------------------
        | 1. SISWA BELUM TKSI
        |--------------------------------------------------------------------------
        */

        $belumTksi = $siswaBelumTksiQuery
            ->with('kelas')
            ->limit(10)
            ->get();


        foreach ($belumTksi as $siswa) {

            $attentionStudents[] = [

                'id' => $siswa->id,

                'name' =>
                    $siswa->nama ?? '-',

                'class' =>
                    $siswa->kelas?->nama_kelas ?? '-',

                'note' =>
                    'Belum melakukan TKSI',

                'status' =>
                    'Belum TKSI',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 2. HASIL TKSI RENDAH
        |--------------------------------------------------------------------------
        */

        if (count($attentionStudents) < 10) {

            $limit = 10 - count($attentionStudents);


            $tksiRendahQuery = TksiHasil::query()
                ->with([
                    'siswa.kelas',
                ])
                ->whereIn(
                    'level',
                    [1, 2]
                );


            if ($periode) {
                $tksiRendahQuery->where(
                    'periode_id',
                    $periode->id
                );
            }


            $tksiRendah = $tksiRendahQuery
                ->latest('tanggal')
                ->limit($limit)
                ->get();


            foreach ($tksiRendah as $hasil) {

                /*
                | Jangan tampilkan siswa yang sudah masuk
                | daftar belum TKSI.
                */

                if (
                    collect($attentionStudents)
                        ->contains('id', $hasil->siswa_id)
                ) {
                    continue;
                }


                $attentionStudents[] = [

                    'id' =>
                        $hasil->siswa_id,

                    'name' =>
                        $hasil->siswa?->nama ?? '-',

                    'class' =>
                        $hasil->siswa?->kelas?->nama_kelas ?? '-',

                    'note' =>
                        $hasil->komponen
                            ? $hasil->komponen .
                                ' - Level ' .
                                ($hasil->level ?? '-')
                            : 'Hasil TKSI rendah',

                    'status' =>
                        'Perlu Perhatian',
                ];


                /*
                | Maksimal 10 peserta.
                */

                if (count($attentionStudents) >= 10) {
                    break;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        $activities = [];


        /*
        |--------------------------------------------------------------------------
        | HASIL TKSI TERBARU
        |--------------------------------------------------------------------------
        */

        $tksiActivitiesQuery = TksiHasil::query()
            ->with('siswa')
            ->latest('tanggal')
            ->limit(10);


        if ($periode) {
            $tksiActivitiesQuery->where(
                'periode_id',
                $periode->id
            );
        }


        $tksiActivities = $tksiActivitiesQuery->get();


        foreach ($tksiActivities as $item) {

            $activities[] = [

                'text' =>
                    'TKSI - ' .
                    ($item->siswa?->nama ?? 'Siswa'),

                'staff' =>
                    $item->komponen
                        ? 'Komponen: ' . $item->komponen
                        : 'Hasil TKSI',

                'time' =>
                    $item->tanggal
                        ? Carbon::parse(
                            $item->tanggal
                        )->diffForHumans()
                        : '-',

                '_date' =>
                    $item->tanggal,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | URUTKAN AKTIVITAS
        |--------------------------------------------------------------------------
        */

        usort(
            $activities,
            function ($a, $b) {

                return strtotime(
                    $b['_date'] ?? '1970-01-01'
                )
                <=>
                strtotime(
                    $a['_date'] ?? '1970-01-01'
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REMINDERS
        |--------------------------------------------------------------------------
        */

        $reminders = [];


        /*
        |--------------------------------------------------------------------------
        | REMINDER SISWA BELUM TKSI
        |--------------------------------------------------------------------------
        */

        if ($totalBelumTksi > 0) {

            $reminders[] = [

                'title' =>
                    $totalBelumTksi .
                    ' siswa belum melakukan TKSI',

                'date' =>
                    $periode?->nama_periode
                    ?? 'Periode aktif',

                'deadline' =>
                    $periode?->tanggal_selesai
                        ? Carbon::parse(
                            $periode->tanggal_selesai
                        )->format('d M Y')
                        : '-',

                'status' =>
                    'Perlu Ditindaklanjuti',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | REMINDER PERIODE AKAN BERAKHIR
        |--------------------------------------------------------------------------
        */

        if (
            $periode &&
            $periode->tanggal_selesai
        ) {

            $tanggalSelesai = Carbon::parse(
                $periode->tanggal_selesai
            );


            $sisaHari = Carbon::now()->diffInDays(
                $tanggalSelesai,
                false
            );


            if (
                $sisaHari >= 0 &&
                $sisaHari <= 7
            ) {

                $reminders[] = [

                    'title' =>
                        'Periode ' .
                        $periode->nama_periode .
                        ' segera berakhir',

                    'date' =>
                        $periode->tanggal_mulai
                            ? Carbon::parse(
                                $periode->tanggal_mulai
                            )->format('d M Y')
                            : '-',

                    'deadline' =>
                        $tanggalSelesai->format('d M Y'),

                    'status' =>
                        'Segera Selesai',
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PERSENTASE TKSI
        |--------------------------------------------------------------------------
        */

        $persentaseTksi = $totalSiswa > 0
            ? round(
                ($totalSudahTksi / $totalSiswa) * 100
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $stats = [

            [
                'name' =>
                    'Total Siswa',

                'value' =>
                    $totalSiswa,

                'sub' =>
                    $periode
                        ? $periode->nama_periode
                        : 'Tidak ada periode aktif',

                'color' =>
                    'border-blue-500',
            ],


            [
                'name' =>
                    'Sudah TKSI',

                'value' =>
                    $totalSudahTksi,

                'sub' =>
                    $persentaseTksi .
                    '% siswa sudah dites',

                'color' =>
                    'border-emerald-500',
            ],


            [
                'name' =>
                    'Belum TKSI',

                'value' =>
                    $totalBelumTksi,

                'sub' =>
                    'Siswa belum mengikuti tes',

                'color' =>
                    'border-amber-500',
            ],


            [
                'name' =>
                    'Kunjungan Klinik',

                'value' =>
                    $totalKunjungan,

                'sub' =>
                    $periode
                        ? $periode->nama_periode
                        : 'Tidak ada periode aktif',

                'color' =>
                    'border-rose-500',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | HAPUS FIELD INTERNAL
        |--------------------------------------------------------------------------
        */

        $activities = collect($activities)
            ->map(function ($item) {

                unset($item['_date']);

                return $item;
            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | RETURN INERTIA
        |--------------------------------------------------------------------------
        |
        | PENTING:
        |
        | File:
        | resources/js/Pages/Tksi/Dashboard/Index.vue
        |
        | Maka:
        | Tksi/Dashboard/Index
        |
        */

        return Inertia::render(
            'Tksi/Dashboard/Index',
            [

                'user' => [

                    'id' =>
                        $user->id,

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,
                ],

                'periode' =>
                    $periode,

                'stats' =>
                    $stats,

                'attentionStudents' =>
                    $attentionStudents,

                'activities' =>
                    $activities,

                'reminders' =>
                    $reminders,
            ]
        );
    }
}