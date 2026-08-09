<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaPeriodeAktifController extends Controller
{
    public function index()
    {
        $periode = Periode::where('status', 'aktif')
            ->with([
    'siswa.kelas.jurusan',
    'pemeriksaanBerkala',
    'tksiBatches.peserta',
])
            ->first();

        if (!$periode) {
            return Inertia::render('Admin/Periode/SiswaAktif', [
                'periode' => null,
                'siswa' => [],
                'statistik' => [
                    'total' => 0,
                    'berkala_1' => 0,
                    'berkala_2' => 0,
                    'tksi' => 0,
                ],
            ]);
        }

        $siswa = $periode->siswa->map(function ($item) use ($periode) {

            $berkala1 = $periode->pemeriksaanBerkala
                ->where('siswa_id', $item->id)
                ->where('jenis_pemeriksaan', 'berkala_1')
                ->first();

            $berkala2 = $periode->pemeriksaanBerkala
                ->where('siswa_id', $item->id)
                ->where('jenis_pemeriksaan', 'berkala_2')
                ->first();

            $tksiSelesai = $periode->tksiBatches
                ->flatMap(function ($batch) {
                    return $batch->peserta;
                })
                ->where('siswa_id', $item->id)
                ->where('status', 'selesai')
                ->isNotEmpty();

            $statusBerkala1 = $berkala1?->status ?? 'belum';
            $statusBerkala2 = $berkala2?->status ?? 'belum';
            $statusTksi = $tksiSelesai ? 'selesai' : 'belum';

            $lengkap =
                $statusBerkala1 === 'selesai' &&
                $statusBerkala2 === 'selesai' &&
                $statusTksi === 'selesai';

            return [
    'id' => $item->id,
    'nisn' => $item->nisn,
    'nama' => $item->nama,

    'kelas' => $item->kelas,

    'jurusan' => $item->kelas?->jurusan,

    'berkala_1' => [
        'status' => $statusBerkala1,
    ],

    'berkala_2' => [
        'status' => $statusBerkala2,
    ],

    'tksi' => [
        'status' => $statusTksi,
    ],

    'status' => $lengkap
        ? 'lengkap'
        : 'belum',
];
        });

        return Inertia::render('Admin/Periode/SiswaAktif', [
            'periode' => [
                'id' => $periode->id,
                'nama_periode' => $periode->nama_periode,
                'tanggal_mulai' => $periode->tanggal_mulai,
                'tanggal_selesai' => $periode->tanggal_selesai,
                'status' => $periode->status,
            ],

            'siswa' => $siswa->values(),

            'statistik' => [
                'total' => $siswa->count(),

                'berkala_1' => $siswa
                    ->where('berkala_1.status', 'selesai')
                    ->count(),

                'berkala_2' => $siswa
                    ->where('berkala_2.status', 'selesai')
                    ->count(),

                'tksi' => $siswa
                    ->where('tksi.status', 'selesai')
                    ->count(),
            ],
        ]);
    }

public function show($siswaId)
{
    $periode = Periode::where('status', 'aktif')
        ->with([
            'siswa.kelas.jurusan',
            'pemeriksaanBerkala',
            'tksiBatches.peserta',
        ])
        ->first();

    if (!$periode) {
        abort(404, 'Tidak ada periode aktif.');
    }

    $siswa = $periode->siswa->firstWhere('id', $siswaId);

    if (!$siswa) {
        abort(404, 'Siswa tidak ditemukan pada periode aktif.');
    }

    // =====================================================
    // BERKALA 1
    // =====================================================

    $berkala1 = $periode->pemeriksaanBerkala
        ->where('siswa_id', $siswa->id)
        ->where('jenis_pemeriksaan', 'berkala_1')
        ->first();

    // =====================================================
    // BERKALA 2
    // =====================================================

    $berkala2 = $periode->pemeriksaanBerkala
        ->where('siswa_id', $siswa->id)
        ->where('jenis_pemeriksaan', 'berkala_2')
        ->first();

    // =====================================================
    // TKSI
    // =====================================================

    $tksiPeserta = $periode->tksiBatches
        ->flatMap(function ($batch) {
            return $batch->peserta;
        });

    $tksi = $tksiPeserta
        ->where('siswa_id', $siswa->id)
        ->first();

    $tksiSelesai = $tksiPeserta
        ->where('siswa_id', $siswa->id)
        ->where('status', 'selesai')
        ->isNotEmpty();

    // =====================================================
    // STATUS
    // =====================================================

    $statusBerkala1 = $berkala1?->status ?? 'belum';

    $statusBerkala2 = $berkala2?->status ?? 'belum';

    $statusTksi = $tksiSelesai
        ? 'selesai'
        : 'belum';

    $lengkap =
        $statusBerkala1 === 'selesai' &&
        $statusBerkala2 === 'selesai' &&
        $statusTksi === 'selesai';

    // =====================================================
    // RESPONSE
    // =====================================================

    return Inertia::render(
        'Admin/Periode/ShowSiswaAktif',
        [
            'periode' => [
                'id' => $periode->id,
                'nama_periode' => $periode->nama_periode,
                'tanggal_mulai' => $periode->tanggal_mulai,
                'tanggal_selesai' => $periode->tanggal_selesai,
                'status' => $periode->status,
            ],

            'siswa' => [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,

                // Kelas sudah membawa Jurusan
                'kelas' => $siswa->kelas,

                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir,
                'jenis_kelamin' => $siswa->jenis_kelamin,

                'berkala_1' => [
                    'status' => $statusBerkala1,
                    'data' => $berkala1,
                ],

                'berkala_2' => [
                    'status' => $statusBerkala2,
                    'data' => $berkala2,
                ],

                'tksi' => [
                    'status' => $statusTksi,
                    'data' => $tksi,
                ],

                'status' => $lengkap
                    ? 'lengkap'
                    : 'belum',
            ],
        ]
    );
}
}