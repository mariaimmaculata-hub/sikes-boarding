<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ObatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/DataObat/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/DataObat/Create');
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.obat.index');
    }

    public function show(string $id)
    {
        return redirect()->route('petugas.obat.index');
    }

    public function edit(string $id): Response
    {
        return Inertia::render('Petugas/DataObat/Edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('petugas.obat.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('petugas.obat.index');
    }
}
