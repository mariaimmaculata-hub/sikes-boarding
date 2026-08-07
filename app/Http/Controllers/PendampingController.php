<?php

namespace App\Http\Controllers;

use App\Models\TksiBatch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PendampingController extends Controller
{
    /**
     * Display the pendamping dashboard.
     */
    public function dashboard(): Response
    {
        return Inertia::render('Pendamping/Dashboard/Index');
    }

    /**
     * Display listing of boarding students.
     */
    public function siswa(): Response
    {
        return Inertia::render('Pendamping/SiswaBoarding/Index');
    }

    /**
     * Display detail profil of a specific student.
     */
    public function siswaShow(string $id): Response
    {
        return Inertia::render('Pendamping/SiswaBoarding/Show', [
            'id' => $id
        ]);
    }

    /**
     * Display periodic examinations.
     */
    public function pemeriksaan(): Response
    {
        return Inertia::render('Pendamping/PemeriksaanBerkala/Index');
    }
    public function pemeriksaanShow($id)
    {
        return Inertia::render(
            'Pendamping/PemeriksaanBerkala/Show',
            [
                'id'=>$id
            ]
        );
    }

    /**
     * Display fitness tests list (TKSI).
     */
    public function tksi(): Response
    {
        return Inertia::render('Pendamping/TKSI/Index');
    }
    public function tksiCreate()
{
    return Inertia::render('Pendamping/TKSI/Create');
}
public function tksiStore(Request $request)
{

    $data = $request->validate([

        'nama_tes'=>'required',
        'tanggal'=>'required',
        'periode'=>'required',

        'kelas'=>'required',
        'jurusan'=>'required',

        'komponen'=>'required',

    ]);


    return redirect()
        ->route('pendamping.tksi.index')
        ->with('success','Tes TKSI berhasil dibuat');

}
public function tksiShow($id)
{
    $batch = TksiBatch::findOrFail($id);

    $siswa = [
        [
            'id'=>1,
            'nis'=>'2024001',
            'nama'=>'Ahmad Fauzi',
            'kelas'=>'X',
            'jurusan'=>'Teknik Komputer dan Jaringan',
            'status'=>'Sudah',
            'kategori'=>'Baik'
        ],
        [
            'id'=>2,
            'nis'=>'2024002',
            'nama'=>'Budi Santoso',
            'kelas'=>'X',
            'jurusan'=>'Teknik Komputer dan Jaringan',
            'status'=>'Belum',
            'kategori'=>'-'
        ],
    ];

    return Inertia::render('Pendamping/TKSI/Show',[
        'batch'=>$batch,
        'siswa'=>$siswa
    ]);
}
    public function tksiPanduan()
{
    return Inertia::render(
        'Pendamping/TKSI/Panduan'
    );
}
public function panduanHandEye(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/HandEye');
}

public function panduanVerticalJump(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/VerticalJump');
}

public function panduanTTest(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/TTest');
}

public function panduanHandTouch(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/HandTouch');
}

public function panduanDipping(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/Dipping');
}

public function panduanBeep(): Response
{
    return Inertia::render('Pendamping/TKSI/Panduan/Beep');
}
    /**
     * Display clinical visits history.
     */
    public function kunjungan(): Response
    {
        return Inertia::render('Pendamping/KunjunganKlinik/Index');
    }

    /**
     * Display reminders and tasks.
     */
    public function pengingat(): Response
    {
        return Inertia::render('Pendamping/PengingatTugas/Index');
    }

    /**
     * Display reports and analytics.
     */
    public function laporan(): Response
    {
        return Inertia::render('Pendamping/Laporan/Index');
    }

    /**
     * Display settings page.
     */
    public function pengaturan(): Response
    {
        return Inertia::render('Pendamping/Pengaturan/Index');
    }
}
