<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaController extends Controller
{
    /**
     * Menampilkan data siswa
     */
    public function index()
    {
        $siswas = Siswa::with([
            'kelas.jurusan'
        ])
        ->orderBy('nama')
        ->paginate(10)
        ->withQueryString();

        return Inertia::render('Admin/MasterData/Siswa/Index', [
            'siswas' => $siswas,
        ]);
    }

    /**
     * Form tambah siswa
     */
    public function create()
    {
        $kelas = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return Inertia::render('Admin/MasterData/Siswa/Create', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Simpan siswa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan' => 'required|integer',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        Siswa::create($validated);

        return redirect()
            ->route('admin.master.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Detail siswa
     */
    public function show(Siswa $siswa)
{
    $siswa->load([
        'kelas.jurusan',
        'periode',
        'pemeriksaanBerkala' => function ($query) {
            $query->latest();
        },
        'kunjunganKlinik' => function ($query) {
            $query->latest();
        },
        'tksiPeserta',
    ]);

    return inertia('Admin/MasterData/Siswa/Show', [
        'siswa' => $siswa,
    ]);
}

    /**
     * Form edit siswa
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return Inertia::render('Admin/MasterData/Siswa/Edit', [
            'siswa' => $siswa,
            'kelas' => $kelas,
        ]);
    }

    /**
     * Update siswa
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan' => 'required|integer',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('admin.master.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa
     */
    public function destroy(Siswa $siswa)
{
    try {
        $siswa->delete();

        return redirect()
            ->route('admin.master.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');

    } catch (\Illuminate\Database\QueryException $e) {

        return redirect()
            ->route('admin.master.siswa.index')
            ->with('error', 'Data siswa tidak dapat dihapus karena masih memiliki riwayat kunjungan klinik.');
    }
}
}