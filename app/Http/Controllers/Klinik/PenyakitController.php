<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PenyakitController extends Controller
{
    public function index()
    {
        $penyakits = Penyakit::orderBy('nama_penyakit')->get();

        return Inertia::render('Klinik/Penyakit/Index', [
            'penyakits' => $penyakits,
        ]);
    }

    public function create()
    {
        return Inertia::render('Klinik/Penyakit/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penyakit' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Penyakit::create($validated);

        return redirect()
            ->route('klinik.penyakit.index')
            ->with('success', 'Data penyakit berhasil ditambahkan.');
    }

    public function edit(Penyakit $penyakit)
    {
        return Inertia::render('Klinik/Penyakit/Edit', [
            'penyakit' => $penyakit,
        ]);
    }

    public function update(Request $request, Penyakit $penyakit)
    {
        $validated = $request->validate([
            'nama_penyakit' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $penyakit->update($validated);

        return redirect()
            ->route('klinik.penyakit.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    public function destroy(Penyakit $penyakit)
    {
        $penyakit->delete();

        return redirect()
            ->route('klinik.penyakit.index')
            ->with('success', 'Data penyakit berhasil dihapus.');
    }
    public function show(Penyakit $penyakit)
{
    return Inertia::render(
        'Klinik/Penyakit/Show',
        [
            'penyakit' => [
                'id' => $penyakit->id,
                'nama_penyakit' => $penyakit->nama_penyakit,
                'kategori' => $penyakit->kategori,
                'keterangan' => $penyakit->keterangan,
            ],
        ]
    );
}
}