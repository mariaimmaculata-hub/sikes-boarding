<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\PemeriksaanBerkala;
use App\Models\KunjunganKlinik;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard Klinik
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $periodeAktif = Periode::where('status', 'aktif')
            ->with('siswa.kelas.jurusan')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | SISWA PADA PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $siswaPeriodeIds = collect();

        $totalSiswa = 0;

        if ($periodeAktif) {
            $siswaPeriodeIds = $periodeAktif
                ->siswa()
                ->pluck('siswas.id');

            $totalSiswa = $siswaPeriodeIds->count();
        }

        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN BERKALA
        |--------------------------------------------------------------------------
        */

        $totalPemeriksaan = 0;
        $pemeriksaanSelesai = 0;
        $pemeriksaanBelum = 0;

        if ($periodeAktif) {

            // Setiap siswa memiliki 2 pemeriksaan berkala
            $totalPemeriksaan = $totalSiswa * 2;

            $pemeriksaanSelesai = PemeriksaanBerkala::where(
                    'periode_id',
                    $periodeAktif->id
                )
                ->whereIn('siswa_id', $siswaPeriodeIds)
                ->count();

            $pemeriksaanBelum = max(
                0,
                $totalPemeriksaan - $pemeriksaanSelesai
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KUNJUNGAN KLINIK
        |--------------------------------------------------------------------------
        */

        $totalKunjungan = 0;

        if ($periodeAktif) {
            $totalKunjungan = KunjunganKlinik::where(
                    'periode_id',
                    $periodeAktif->id
                )
                ->whereIn('siswa_id', $siswaPeriodeIds)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | SISWA PERLU PERHATIAN
        |--------------------------------------------------------------------------
        */

        $attentionStudents = collect();

        if ($periodeAktif) {

            $attentionStudents = KunjunganKlinik::with([
                    'siswa.kelas.jurusan'
                ])
                ->where('periode_id', $periodeAktif->id)
                ->whereIn('siswa_id', $siswaPeriodeIds)
                ->latest('tanggal_kunjungan')
                ->take(5)
                ->get()
                ->map(function ($kunjungan) {

                    return [
                        'id' => $kunjungan->siswa?->id,

                        'name' => $kunjungan->siswa?->nama ?? '-',

                        'class' => $kunjungan->siswa?->kelas
                            ? $kunjungan->siswa->kelas->nama_kelas
                            : '-',

                        'note' => $kunjungan->diagnosis
                            ?? $kunjungan->keluhan
                            ?? 'Kunjungan klinik',

                        'status' => $kunjungan->status
                            ?? 'Perlu Perhatian',
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        $activities = collect();

        if ($periodeAktif) {

            $activities = KunjunganKlinik::with('siswa')
                ->where('periode_id', $periodeAktif->id)
                ->whereIn('siswa_id', $siswaPeriodeIds)
                ->latest('tanggal_kunjungan')
                ->take(5)
                ->get()
                ->map(function ($kunjungan) {

                    return [
                        'text' => 'Kunjungan klinik ' .
                            ($kunjungan->siswa?->nama ?? 'siswa'),

                        'staff' => 'Pemeriksaan kesehatan',

                        'time' => $kunjungan->tanggal_kunjungan
                            ? $kunjungan->tanggal_kunjungan->format('d M Y')
                            : '-',
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | PENGINGAT
        |--------------------------------------------------------------------------
        */

        $reminders = [];

        if ($pemeriksaanBelum > 0) {

            $reminders[] = [
                'title' => 'Pemeriksaan berkala belum selesai',

                'date' => $periodeAktif?->nama_periode ?? '-',

                'deadline' => 'Segera diselesaikan',

                'status' => $pemeriksaanBelum . ' pemeriksaan',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Klinik/Dashboard/Index', [

            'user' => $user,

            'periode' => $periodeAktif
                ? [
                    'id' => $periodeAktif->id,
                    'nama_periode' => $periodeAktif->nama_periode,
                    'tanggal_mulai' => $periodeAktif->tanggal_mulai,
                    'tanggal_selesai' => $periodeAktif->tanggal_selesai,
                    'status' => $periodeAktif->status,
                ]
                : null,

            'stats' => [

                [
                    'name' => 'Total Siswa',

                    'value' => $totalSiswa,

                    'sub' => $periodeAktif
                        ? 'Siswa pada periode aktif'
                        : 'Belum ada periode aktif',

                    'link' => false,

                    'color' => 'border-blue-600 text-blue-600',
                ],

                [
                    'name' => 'Pemeriksaan',

                    'value' => $pemeriksaanSelesai,

                    'sub' => $pemeriksaanBelum . ' belum selesai',

                    'link' => true,

                    'color' => 'border-rose-500 text-rose-500',
                ],

                [
                    'name' => 'Kunjungan Klinik',

                    'value' => $totalKunjungan,

                    'sub' => $periodeAktif
                        ? 'Pada periode aktif'
                        : 'Belum ada periode aktif',

                    'link' => false,

                    'color' => 'border-orange-500 text-orange-500',
                ],

                [
                    'name' => 'Periode Aktif',

                    'value' => $periodeAktif
                        ? $periodeAktif->nama_periode
                        : '-',

                    'sub' => $periodeAktif
                        ? 'Sedang berjalan'
                        : 'Belum ada periode',

                    'link' => false,

                    'color' => 'border-purple-500 text-purple-500',
                ],
            ],

            'attentionStudents' => $attentionStudents,

            'activities' => $activities,

            'reminders' => $reminders,
        ]);
    }
}

