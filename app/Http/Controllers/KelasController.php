<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/MasterData/Kelas/Index');
    }

    public function showJurusan(string $jurusan): Response
    {
        return Inertia::render('Petugas/MasterData/Kelas/Jurusan/Show', [
            'jurusan' => $jurusan
        ]);
    }

    public function showKelas(string $jurusan, string $kelas): Response
    {
        return Inertia::render('Petugas/MasterData/Kelas/Kelas/Show', [
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ]);
    }
}
