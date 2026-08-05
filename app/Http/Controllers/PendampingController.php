<?php

namespace App\Http\Controllers;

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

    /**
     * Display fitness tests list (TKSI).
     */
    public function tksi(): Response
    {
        return Inertia::render('Pendamping/TKSI/Index');
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
