<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PemeriksaanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/PemeriksaanBerkala/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/PemeriksaanBerkala/Create');
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.pemeriksaan.index');
    }

    public function show(string $id): Response
    {
        return Inertia::render('Petugas/PemeriksaanBerkala/Show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return redirect()->route('petugas.pemeriksaan.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('petugas.pemeriksaan.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('petugas.pemeriksaan.index');
    }
}
