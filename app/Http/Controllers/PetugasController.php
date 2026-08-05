<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PetugasController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Petugas/Dashboard/Index');
    }

    public function laporan(): Response
    {
        return Inertia::render('Petugas/Laporan/Index');
    }

    public function pengaturan(): Response
    {
        return Inertia::render('Petugas/Pengaturan/Index');
    }
}
