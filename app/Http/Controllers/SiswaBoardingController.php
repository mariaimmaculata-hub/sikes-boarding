<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiswaBoardingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Pendamping/SiswaBoarding/Index');
    }

    public function showJurusan(string $jurusan): Response
    {
        return Inertia::render('Pendamping/SiswaBoarding/Jurusan/Show', [
            'jurusan' => $jurusan
        ]);
    }

    public function showKelas(string $jurusan, string $kelas): Response
    {
        return Inertia::render('Pendamping/SiswaBoarding/Kelas/Show', [
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ]);
    }

    public function showSiswa(string $siswa): Response
    {
        // For detail page, just redirect to Level 3 or render list
        return redirect()->route('pendamping.siswa.index');
    }
}
