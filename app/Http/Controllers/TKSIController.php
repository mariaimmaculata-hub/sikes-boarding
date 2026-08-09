<?php

namespace App\Http\Controllers;

use App\Models\TksiBatch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TksiController extends Controller
{
    /**
     * Dashboard TKSI
     */
    public function dashboard(): Response
    {
        return Inertia::render('Tksi/Dashboard/Index');
    }

    /**
     * Data siswa
     */
    public function siswa(): Response
    {
        return Inertia::render('Tksi/Siswa/Index');
    }

    /**
     * Detail siswa
     */
    public function siswaShow(string $id): Response
    {
        return Inertia::render('Tksi/Siswa/Show', [
            'id' => $id,
        ]);
    }

    /**
     * Daftar batch TKSI
     */
    public function tksi(): Response
    {
        return Inertia::render('Tksi/TKSI/Index');
    }

    /**
     * Form tambah batch TKSI
     */
    public function tksiCreate(): Response
    {
        return Inertia::render('Tksi/TKSI/Create');
    }

    /**
     * Simpan batch TKSI
     */
    public function tksiStore(Request $request)
    {
        $data = $request->validate([
            'nama_tes' => 'required',
            'tanggal' => 'required',
            'periode' => 'required',
            'kelas' => 'required',
            'jurusan' => 'required',
            'komponen' => 'required',
        ]);

        /*
         * Untuk sementara validasi dan redirect tetap dipertahankan
         * dari sistem lama.
         *
         * Nanti pada tahap database TKSI akan kita ubah agar benar-benar
         * membuat data ke tabel tksi_batches.
         */

        return redirect()
            ->route('tksi.input.index')
            ->with('success', 'Tes TKSI berhasil dibuat');
    }

    /**
     * Detail batch TKSI
     */
    public function tksiShow($id): Response
    {
        $batch = TksiBatch::findOrFail($id);

        /*
         * DATA SISWA DUMMY LAMA DIHAPUS.
         *
         * Nanti siswa akan diambil dari:
         *
         * siswa
         *    ↓
         * periode_siswa
         *    ↓
         * periode aktif
         *
         * Kemudian dikaitkan dengan batch TKSI.
         */

        return Inertia::render('Tksi/TKSI/Show', [
            'batch' => $batch,
        ]);
    }

    /**
     * Input hasil tes siswa
     */
    public function tksiIsi($id): Response
    {
        return Inertia::render('Tksi/TKSI/Isi', [
            'id' => $id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Panduan TKSI
    |--------------------------------------------------------------------------
    */

    public function tksiPanduan(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan');
    }

    public function panduanHandEye(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/HandEye');
    }

    public function panduanVerticalJump(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/VerticalJump');
    }

    public function panduanTTest(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/TTest');
    }

    public function panduanHandTouch(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/HandTouch');
    }

    public function panduanDipping(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/Dipping');
    }

    public function panduanBeep(): Response
    {
        return Inertia::render('Tksi/TKSI/Panduan/Beep');
    }

    /*
    |--------------------------------------------------------------------------
    | Report
    |--------------------------------------------------------------------------
    */

    public function reportPeriode(): Response
    {
        return Inertia::render('Tksi/Report/Periode');
    }

    /*
    |--------------------------------------------------------------------------
    | Notifikasi
    |--------------------------------------------------------------------------
    */

    public function notifikasi(): Response
    {
        return Inertia::render('Tksi/Notifikasi/Index');
    }
}