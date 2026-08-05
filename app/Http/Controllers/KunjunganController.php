<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KunjunganController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/KunjunganKlinik/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/KunjunganKlinik/Create');
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.kunjungan.index');
    }

    public function show(string $id): Response
    {
        return Inertia::render('Petugas/KunjunganKlinik/Show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return redirect()->route('petugas.kunjungan.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('petugas.kunjungan.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('petugas.kunjungan.index');
    }
}
