<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PenyakitController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/DataPenyakit/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/DataPenyakit/Create');
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.penyakit.index');
    }

    public function show(string $id)
    {
        return redirect()->route('petugas.penyakit.index');
    }

    public function edit(string $id): Response
    {
        return Inertia::render('Petugas/DataPenyakit/Edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('petugas.penyakit.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('petugas.penyakit.index');
    }
}
