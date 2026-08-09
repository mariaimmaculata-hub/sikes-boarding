<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KunjunganController extends Controller
{
    public function index(Request $request)
    {
        $query = KunjunganKlinik::with([
            'siswa.kelas',
            'periode',
        ])
        ->latest('tanggal_kunjungan');

        // =====================================================
        // PENCARIAN
        // =====================================================

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // =====================================================
        // FILTER PERIODE
        // =====================================================

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        // =====================================================
        // FILTER STATUS
        // =====================================================

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // =====================================================
        // PAGINATION
        // =====================================================

        $kunjungan = $query
            ->paginate(10)
            ->withQueryString();

        // =====================================================
        // DATA PERIODE UNTUK FILTER
        // =====================================================

        $periodes = Periode::orderByDesc('tanggal_mulai')
            ->get([
                'id',
                'nama_periode',
            ]);

        // =====================================================
        // STATISTIK
        // =====================================================

        $totalKunjungan = KunjunganKlinik::count();

        $kunjunganSelesai = KunjunganKlinik::where(
            'status',
            'selesai'
        )->count();

        $kunjunganProses = KunjunganKlinik::where(
            'status',
            'proses'
        )->count();

        return Inertia::render('Admin/KunjunganKlinik/Index', [
            'kunjungan' => $kunjungan,

            'periodes' => $periodes,

            'filters' => [
                'search' => $request->search,
                'periode_id' => $request->periode_id,
                'status' => $request->status,
            ],

            'statistics' => [
                'total' => $totalKunjungan,
                'selesai' => $kunjunganSelesai,
                'proses' => $kunjunganProses,
            ],
        ]);
    }

    public function show(KunjunganKlinik $kunjungan)
    {
        $kunjungan->load([
            'siswa.kelas',
            'periode',
            'pemeriksa',
            'kunjunganObat',
        ]);

        return Inertia::render('Admin/KunjunganKlinik/Show', [
            'kunjungan' => $kunjungan,
        ]);
    }
}