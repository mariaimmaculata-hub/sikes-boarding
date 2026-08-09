<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\PemeriksaanBerkala;
use App\Models\KunjunganKlinik;
use App\Models\Penyakit;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // STATISTIK
        // =========================

        $totalSiswa = Siswa::where('status', 'aktif')->count();

        $totalKelas = Kelas::count();

        $pegawaiKlinik = User::where('role', 'klinik')->count();

        $pendamping = User::where('role', 'tksi')->count();

        $pemeriksaanHariIni = PemeriksaanBerkala::whereDate(
            'tanggal_pemeriksaan',
            today()
        )->count();


        // =========================
        // 5 PENYAKIT TERBANYAK
        // =========================

        $penyakitTerbanyak = KunjunganKlinik::query()
    ->select('diagnosis')
    ->selectRaw('COUNT(*) as jumlah_kasus')
    ->whereNotNull('diagnosis')
    ->where('diagnosis', '!=', '')
    ->whereMonth('tanggal_kunjungan', now()->month)
    ->whereYear('tanggal_kunjungan', now()->year)
    ->groupBy('diagnosis')
    ->orderByDesc('jumlah_kasus')
    ->limit(5)
    ->get()
    ->map(function ($item) {
        return [
            'name' => $item->diagnosis,
            'total' => $item->jumlah_kasus,
        ];
    });


        // =========================
        // GRAFIK PEMERIKSAAN
        // =========================

        $pemeriksaanBulanan = collect(range(2, 0))->map(function ($i) {

            $date = now()->subMonths($i);

            return [
                'label' => $date->translatedFormat('F'),
                'value' => PemeriksaanBerkala::whereMonth(
                    'tanggal_pemeriksaan',
                    $date->month
                )
                ->whereYear(
                    'tanggal_pemeriksaan',
                    $date->year
                )
                ->count(),
            ];
        });


        // =========================
        // GRAFIK KUNJUNGAN KLINIK
        // =========================

        $kunjunganBulanan = collect(range(2, 0))->map(function ($i) {

            $date = now()->subMonths($i);

            return [
                'label' => $date->translatedFormat('F'),
                'value' => KunjunganKlinik::whereMonth(
                    'tanggal_kunjungan',
                    $date->month
                )
                ->whereYear(
                    'tanggal_kunjungan',
                    $date->year
                )
                ->count(),
            ];
        });


        return Inertia::render('Admin/Dashboard/Index', [

            'stats' => [
                [
                    'name' => 'Total Siswa',
                    'value' => $totalSiswa,
                    'type' => 'siswa',
                ],
                [
                    'name' => 'Total Kelas',
                    'value' => $totalKelas,
                    'type' => 'kelas',
                ],
                [
                    'name' => 'Pegawai Klinik',
                    'value' => $pegawaiKlinik,
                    'type' => 'klinik',
                ],
                [
                    'name' => 'Pendamping Siswa',
                    'value' => $pendamping,
                    'type' => 'pendamping',
                ],
                [
                    'name' => 'Pemeriksaan Hari Ini',
                    'value' => $pemeriksaanHariIni,
                    'type' => 'pemeriksaan',
                ],
            ],

            'penyakitTerbanyak' => $penyakitTerbanyak,

            'pemeriksaanBulanan' => $pemeriksaanBulanan,

            'kunjunganBulanan' => $kunjunganBulanan,
        ]);
    }
}