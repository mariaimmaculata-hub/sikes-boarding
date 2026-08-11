<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TksiBatch;
use App\Models\TksiBatchSiswa;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTAL SISWA AKTIF
        |--------------------------------------------------------------------------
        */

        $totalSiswa = Siswa::where('status', 'aktif')->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL BATCH TKSI
        |--------------------------------------------------------------------------
        */

        $totalBatch = TksiBatch::count();


        /*
        |--------------------------------------------------------------------------
        | PESERTA SUDAH TKSI
        |--------------------------------------------------------------------------
        |
        | Peserta dianggap sudah selesai apabila status = selesai.
        |
        */

        $pesertaSelesai = TksiBatchSiswa::where('status', 'selesai')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PESERTA BELUM TKSI
        |--------------------------------------------------------------------------
        */

        $pesertaBelum = TksiBatchSiswa::where('status', '!=', 'selesai')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $stats = [
            [
                'name' => 'Total Siswa',
                'value' => $totalSiswa,
                'sub' => 'Siswa aktif',
                'color' => 'border-blue-600',
            ],
            [
                'name' => 'Batch TKSI',
                'value' => $totalBatch,
                'sub' => 'Total pelaksanaan TKSI',
                'color' => 'border-emerald-600',
            ],
            [
                'name' => 'Sudah TKSI',
                'value' => $pesertaSelesai,
                'sub' => 'Peserta selesai',
                'color' => 'border-purple-600',
            ],
            [
                'name' => 'Belum TKSI',
                'value' => $pesertaBelum,
                'sub' => 'Peserta perlu diisi',
                'color' => 'border-rose-600',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | SISWA / PESERTA PERLU PERHATIAN
        |--------------------------------------------------------------------------
        */

        $attentionStudents = TksiBatchSiswa::with([
                'siswa.kelas',
                'batch',
            ])
            ->where('status', '!=', 'selesai')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($peserta) {
                return [
                    'id' => $peserta->id,
                    'name' => $peserta->siswa?->nama ?? '-',
                    'class' => $peserta->siswa?->kelas?->nama_kelas ?? '-',
                    'note' => $peserta->batch?->nama_tes ?? 'TKSI',
                    'status' => $peserta->status ?? 'Belum',
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        $activities = TksiBatch::with('periode')
            ->latest('tanggal')
            ->take(5)
            ->get()
            ->map(function ($batch) {
                return [
                    'text' => 'Batch TKSI: ' . $batch->nama_tes,
                    'staff' => $batch->periode?->nama_periode ?? 'Periode aktif',
                    'time' => $batch->tanggal
                        ? $batch->tanggal->format('d M Y')
                        : '-',
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | JADWAL & PENGINGAT
        |--------------------------------------------------------------------------
        */

        $reminders = TksiBatch::with('periode')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->take(5)
            ->get()
            ->map(function ($batch) {
                return [
                    'title' => $batch->nama_tes,
                    'date' => $batch->tanggal
                        ? $batch->tanggal->format('d M Y')
                        : '-',
                    'deadline' => $batch->periode?->tanggal_selesai
                        ? $batch->periode->tanggal_selesai->format('d M Y')
                        : '-',
                    'status' => 'Terjadwal',
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Tksi/Dashboard/Index', [
            'user' => Auth::user(),
            'stats' => $stats,
            'attentionStudents' => $attentionStudents,
            'activities' => $activities,
            'reminders' => $reminders,
        ]);
    }
}
