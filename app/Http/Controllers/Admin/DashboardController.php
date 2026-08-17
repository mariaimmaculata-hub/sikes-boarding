<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\PemeriksaanBerkala;
use App\Models\KunjunganKlinik;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | STATISTIK UTAMA
        |--------------------------------------------------------------------------
        */

        // Total siswa aktif
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // Total kelas
        $totalKelas = Kelas::count();

        // Total pegawai klinik
        $pegawaiKlinik = User::where('role', 'klinik')->count();

        // Total petugas TKSI
        $petugasTksi = User::where('role', 'tksi')->count();

        // Total pemeriksaan berkala hari ini
        $pemeriksaanHariIni = PemeriksaanBerkala::whereDate(
            'tanggal_pemeriksaan',
            today()
        )->count();


        /*
        |--------------------------------------------------------------------------
        | 5 PENYAKIT TERBANYAK BULAN INI
        |--------------------------------------------------------------------------
        */

        $penyakitTerbanyak = KunjunganKlinik::query()
            ->select('diagnosis')
            ->selectRaw('COUNT(*) as jumlah_kasus')
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->whereMonth(
                'tanggal_kunjungan',
                now()->month
            )
            ->whereYear(
                'tanggal_kunjungan',
                now()->year
            )
            ->groupBy('diagnosis')
            ->orderByDesc('jumlah_kasus')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->diagnosis,
                    'count' => (int) $item->jumlah_kasus,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | GRAFIK PEMERIKSAAN BERKALA
        | 3 BULAN TERAKHIR
        |--------------------------------------------------------------------------
        */

        $pemeriksaanBulanan = collect(range(2, 0))
            ->map(function ($i) {

                $date = Carbon::now()
                    ->copy()
                    ->subMonths($i);

                $jumlah = PemeriksaanBerkala::query()
                    ->whereMonth(
                        'tanggal_pemeriksaan',
                        $date->month
                    )
                    ->whereYear(
                        'tanggal_pemeriksaan',
                        $date->year
                    )
                    ->count();

                return [
                    'label' => $date->translatedFormat('M'),
                    'value' => (int) $jumlah,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | GRAFIK KUNJUNGAN KLINIK
        | 3 BULAN TERAKHIR
        |--------------------------------------------------------------------------
        */

        $kunjunganBulanan = collect(range(2, 0))
            ->map(function ($i) {

                $date = Carbon::now()
                    ->copy()
                    ->subMonths($i);

                $jumlah = KunjunganKlinik::query()
                    ->whereMonth(
                        'tanggal_kunjungan',
                        $date->month
                    )
                    ->whereYear(
                        'tanggal_kunjungan',
                        $date->year
                    )
                    ->count();

                return [
                    'label' => $date->translatedFormat('M'),
                    'value' => (int) $jumlah,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | DATA TAMBAHAN
        |--------------------------------------------------------------------------
        |
        | Sementara dikosongkan karena belum ada sumber tabel/model
        | yang digunakan untuk data tersebut.
        |
        */

        $jadwalHariIni = [];

        $siswaPemantauan = [];

        $notifications = [];


        /*
        |--------------------------------------------------------------------------
        | RETURN INERTIA
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Admin/Dashboard/Index',
            [

                /*
                |--------------------------------------------------------------------------
                | STATISTICS
                |--------------------------------------------------------------------------
                */

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
                        'name' => 'Petugas TKSI',
                        'value' => $petugasTksi,
                        'type' => 'pendamping',
                    ],

                    [
                        'name' => 'Pemeriksaan Hari Ini',
                        'value' => $pemeriksaanHariIni,
                        'type' => 'pemeriksaan',
                    ],

                ],

                /*
                |--------------------------------------------------------------------------
                | PENYAKIT
                |--------------------------------------------------------------------------
                */

                'penyakitTerbanyak' => $penyakitTerbanyak,

                /*
                |--------------------------------------------------------------------------
                | GRAFIK
                |--------------------------------------------------------------------------
                */

                'pemeriksaanBulanan' => $pemeriksaanBulanan,

                'kunjunganBulanan' => $kunjunganBulanan,

                /*
                |--------------------------------------------------------------------------
                | DATA TAMBAHAN
                |--------------------------------------------------------------------------
                */

                'jadwalHariIni' => $jadwalHariIni,

                'siswaPemantauan' => $siswaPemantauan,

                'notifications' => $notifications,

            ]
        );
    }
}