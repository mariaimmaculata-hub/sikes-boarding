<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Penyakit;
use App\Services\NotificationService;
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

        $penyakit = Penyakit::create($validated);

        NotificationService::toRole(
            'klinik',
            'Data Penyakit Ditambahkan',
            "Penyakit {$penyakit->nama_penyakit} telah ditambahkan.",
            'info',
            route('klinik.penyakit.index')
        );

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

        NotificationService::toRole(
            'klinik',
            'Data Penyakit Diperbarui',
            "Penyakit {$penyakit->nama_penyakit} telah diperbarui.",
            'info',
            route('klinik.penyakit.index')
        );

        return redirect()
            ->route('klinik.penyakit.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    public function destroy(Penyakit $penyakit)
    {
        $deletedDisease = $penyakit->nama_penyakit;
        $penyakit->delete();

        NotificationService::toRole(
            'klinik',
            'Data Penyakit Dihapus',
            "Penyakit {$deletedDisease} telah dihapus.",
            'warning',
            route('klinik.penyakit.index')
        );

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