<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Siswa;
use Inertia\Inertia;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa berdasarkan periode aktif.
     */
    public function index()
    {
        // ==================================================
        // PERIODE AKTIF
        // ==================================================

        $periodeAktif = Periode::where('status', 'aktif')
            ->first();

        // ==================================================
        // JIKA TIDAK ADA PERIODE AKTIF
        // ==================================================

        if (!$periodeAktif) {
            return Inertia::render('Klinik/Siswa/Index', [
                'periode' => null,

                'siswas' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
            ]);
        }

        // ==================================================
        // SISWA PADA PERIODE AKTIF
        // ==================================================

        $siswas = $periodeAktif
            ->siswa()
            ->with([
                'kelas.jurusan',
            ])
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        // ==================================================
        // RESPONSE
        // ==================================================

        return Inertia::render('Klinik/Siswa/Index', [
            'periode' => [
                'id' => $periodeAktif->id,
                'nama_periode' => $periodeAktif->nama_periode,
                'tanggal_mulai' => $periodeAktif->tanggal_mulai,
                'tanggal_selesai' => $periodeAktif->tanggal_selesai,
                'status' => $periodeAktif->status,
            ],

            'siswas' => $siswas,
        ]);
    }


    /**
     * Menampilkan detail siswa pada periode aktif.
     */
 public function show(Siswa $siswa)
{
    // ==================================================
    // PERIODE AKTIF
    // HANYA UNTUK MENENTUKAN SISWA YANG DAPAT DIBUKA
    // ==================================================

    $periodeAktif = Periode::where('status', 'aktif')
        ->firstOrFail();


    // ==================================================
    // AMBIL SISWA PADA PERIODE AKTIF
    // ==================================================

    $siswa = $periodeAktif
        ->siswa()
        ->where('siswas.id', $siswa->id)
        ->with([

            // ==================================================
            // DATA SISWA
            // ==================================================

            'kelas.jurusan',

            // ==================================================
            // PERIODE SISWA
            // ==================================================

            'periode',

            // ==================================================
            // PEMERIKSAAN BERKALA
            // TETAP HANYA PERIODE AKTIF
            // ==================================================

            'pemeriksaanBerkala' => function ($query) use ($periodeAktif) {

                $query
                    ->where('periode_id', $periodeAktif->id)
                    ->with([
                        'pemeriksa',
                    ])
                    ->orderBy('jenis_pemeriksaan')
                    ->orderBy('tanggal_pemeriksaan');
            },

            // ==================================================
            // RIWAYAT KUNJUNGAN KLINIK
            // SEMUA PERIODE
            // ==================================================

            'kunjunganKlinik' => function ($query) {

                $query
                    ->with([
                        'pemeriksa',
                        'periode',
                        'penyakit',
                        'kunjunganObat.obat',
                    ])
                    ->latest('tanggal_kunjungan');
            },

            // ==================================================
            // TKSI
            // ==================================================

            'tksiPeserta',

        ])
        ->firstOrFail();


    // ==================================================
    // RESPONSE
    // ==================================================

    return Inertia::render(
        'Klinik/Siswa/Show',
        [
            'siswa' => $siswa,

            'periode' => [
                'id' => $periodeAktif->id,
                'nama_periode' => $periodeAktif->nama_periode,
                'tanggal_mulai' => $periodeAktif->tanggal_mulai,
                'tanggal_selesai' => $periodeAktif->tanggal_selesai,
                'status' => $periodeAktif->status,
            ],
        ]
    );
}
}
