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
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DATA SISWA
        |--------------------------------------------------------------------------
        */

        $query = Siswa::with([
            'kelas.jurusan'
        ]);


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        |
        | Cari berdasarkan:
        | - NISN
        | - Nama
        | - Tingkat kelas
        | - Nama kelas
        | - Jurusan
        |
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")

                    ->orWhereHas('kelas', function ($kelas) use ($search) {

                        $kelas->where('tingkat', 'like', "%{$search}%")
                            ->orWhere('nama_kelas', 'like', "%{$search}%")

                            ->orWhereHas('jurusan', function ($jurusan) use ($search) {

                                $jurusan->where(
                                    'nama_jurusan',
                                    'like',
                                    "%{$search}%"
                                );

                            });

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER ANGKATAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('angkatan')) {

            $query->where(
                'angkatan',
                $request->angkatan
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER JENIS KELAMIN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('jenis_kelamin')) {

            $query->where(
                'jenis_kelamin',
                $request->jenis_kelamin
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER KELAS
        |--------------------------------------------------------------------------
        |
        | Filter berdasarkan tingkat:
        | 10 / 11 / 12
        |
        */

        if ($request->filled('kelas')) {

            $query->whereHas('kelas', function ($kelas) use ($request) {

                $kelas->where(
                    'tingkat',
                    $request->kelas
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | DATA ANGKATAN UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        |
        | Diambil dari seluruh database,
        | bukan hanya dari halaman pagination aktif.
        |
        */

        $angkatanOptions = Siswa::query()
            ->whereNotNull('angkatan')
            ->where('angkatan', '!=', '')
            ->distinct()
            ->orderByDesc('angkatan')
            ->pluck('angkatan');


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $siswas = $query
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Admin/MasterData/Siswa/Index', [
            'siswas' => $siswas,
            'angkatanOptions' => $angkatanOptions,
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
                ->with(
                    'error',
                    'Data siswa tidak dapat dihapus karena masih memiliki riwayat kunjungan klinik.'
                );
        }
    }
    public function bulkStatus(Request $request)
{
    $validated = $request->validate([
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['integer', 'exists:siswas,id'],
        'status' => ['required', 'in:nonaktif,lulus'],
    ]);

    Siswa::whereIn('id', $validated['ids'])
        ->update([
            'status' => $validated['status'],
        ]);

    $jumlah = count($validated['ids']);

    $label = $validated['status'] === 'lulus'
        ? 'Lulus'
        : 'Nonaktif';

    return back()->with(
        'success',
        "{$jumlah} siswa berhasil diubah menjadi {$label}."
    );
}
public function bulkNaikKelas(Request $request)
{
    $validated = $request->validate([
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['integer', 'exists:siswas,id'],
    ]);

    $siswas = Siswa::with('kelas')
        ->whereIn('id', $validated['ids'])
        ->get();

    $jumlahNaik = 0;
    $jumlahLulus = 0;

    foreach ($siswas as $siswa) {

        if (!$siswa->kelas) {
            continue;
        }

        $tingkatSekarang = (int) $siswa->kelas->tingkat;


        // ==========================================
        // KELAS 10 → KELAS 11
        // ==========================================

        if ($tingkatSekarang === 10) {

            $kelasBaru = Kelas::where('tingkat', 11)
                ->where('jurusan_id', $siswa->kelas->jurusan_id)
                ->first();

            if ($kelasBaru) {

                $siswa->update([
                    'kelas_id' => $kelasBaru->id,
                    'status' => 'aktif',
                ]);

                $jumlahNaik++;
            }

            continue;
        }


        // ==========================================
        // KELAS 11 → KELAS 12
        // ==========================================

        if ($tingkatSekarang === 11) {

            $kelasBaru = Kelas::where('tingkat', 12)
                ->where('jurusan_id', $siswa->kelas->jurusan_id)
                ->first();

            if ($kelasBaru) {

                $siswa->update([
                    'kelas_id' => $kelasBaru->id,
                    'status' => 'aktif',
                ]);

                $jumlahNaik++;
            }

            continue;
        }


        // ==========================================
        // KELAS 12 → LULUS
        // ==========================================

        if ($tingkatSekarang === 12) {

            $siswa->update([
                'status' => 'lulus',
            ]);

            $jumlahLulus++;
        }
    }


    return back()->with(
        'success',
        "{$jumlahNaik} siswa naik kelas dan {$jumlahLulus} siswa dinyatakan lulus."
    );
}
}