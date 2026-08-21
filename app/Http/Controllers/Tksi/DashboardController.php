<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\PeriodeSiswa;
use App\Models\TksiHasil;
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
        | TOTAL PESERTA PADA PERIODE AKTIF
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
        | PESERTA YANG SUDAH MELAKUKAN TKSI
        |--------------------------------------------------------------------------
        |
        | Satu siswa dapat memiliki beberapa hasil TKSI
        | berdasarkan komponen tes.
        |
        | Karena itu siswa dihitung DISTINCT berdasarkan siswa_id.
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
        | PESERTA YANG BELUM MELAKUKAN TKSI
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
        | URUTKAN AKTIVITAS TERBARU
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
        | REMINDER PESERTA BELUM TKSI
        |--------------------------------------------------------------------------
        */

        if ($totalBelumTksi > 0) {

            $reminders[] = [

                'title' =>
                    $totalBelumTksi .
                    ' peserta belum melakukan TKSI',

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
        |
        | DASHBOARD KHUSUS TKSI
        |
        | Hanya menampilkan:
        |
        | 1. Total Peserta
        | 2. Sudah TKSI
        | 3. Belum TKSI
        |
        */

        $stats = [

            [
                'name' =>
                    'Total Peserta',

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
                    '% peserta sudah dites',

                'color' =>
                    'border-emerald-500',
            ],


            [
                'name' =>
                    'Belum TKSI',

                'value' =>
                    $totalBelumTksi,

                'sub' =>
                    'Peserta belum mengikuti tes',

                'color' =>
                    'border-amber-500',
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | HAPUS FIELD INTERNAL AKTIVITAS
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

                'activities' =>
                    $activities,

                'reminders' =>
                    $reminders,
            ]
        );
    }
}