<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::orderBy('nama_obat')
            ->get();

        return Inertia::render('Klinik/Obat/Index', [
            'obats' => $obats,
        ]);
    }

    public function create()
    {
        return Inertia::render('Klinik/Obat/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:100'],
            'stok' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
        ]);

        Obat::create($validated);

        return redirect()
            ->route('klinik.obat.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Obat $obat)
    {
        return Inertia::render('Klinik/Obat/Edit', [
            'obat' => $obat,
        ]);
    }

    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:100'],
            'stok' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
        ]);

        $obat->update($validated);

        return redirect()
            ->route('klinik.obat.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        try {
            $obat->delete();

            return redirect()
                ->route('klinik.obat.index')
                ->with('success', 'Data obat berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('klinik.obat.index')
                ->with(
                    'error',
                    'Obat tidak dapat dihapus karena sudah digunakan pada data kunjungan klinik.'
                );
        }
    }
}