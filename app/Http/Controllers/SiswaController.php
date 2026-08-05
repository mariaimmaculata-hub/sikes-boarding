<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiswaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/MasterData/Siswa/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/MasterData/Siswa/Index'); // Inline modal-based CRUD
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.master.siswa.index');
    }

    public function show($id)
    {
        return redirect()->route('petugas.master.siswa.index');
    }

    public function edit($id)
    {
        return redirect()->route('petugas.master.siswa.index');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('petugas.master.siswa.index');
    }

    public function destroy($id)
    {
        return redirect()->route('petugas.master.siswa.index');
    }
}
