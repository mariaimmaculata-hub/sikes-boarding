<?php

namespace App\Http\Controllers\Admin;

use App\Models\PemeriksaanBerkala;
use App\Models\KunjunganKlinik;
use App\Models\TksiBatch;
use App\Models\TksiBatchSiswa;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    /**
     * Menampilkan daftar periode.
     */
    public function index()
    {
        $periodes = Periode::with('pembuat')
            ->withCount('siswa')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Periode/Index', [
            'periodes' => $periodes,
        ]);
    }


    /**
     * Form tambah periode.
     */
    public function create()
    {
        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return Inertia::render('Admin/Periode/Create', [
            'siswas' => $siswas,
        ]);
    }


    /**
     * Menyimpan periode baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'status' => [
    'required',
    'in:aktif,selesai,draft',
],

            'siswa_ids' => [
                'nullable',
                'array',
            ],

            'siswa_ids.*' => [
                'integer',
                'exists:siswas,id',
            ],
        ]);


        DB::transaction(function () use (
            $validated,
            $request
        ) {

            $periode = Periode::create([
                'nama_periode' => $validated['nama_periode'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'status' => $validated['status'],
                'created_by' => $request->user()->id,
            ]);


            $periode->siswa()->sync(
                $validated['siswa_ids'] ?? []
            );
        });


        return redirect()
            ->route('admin.periode.index')
            ->with(
                'success',
                'Periode berhasil ditambahkan.'
            );
    }


    /**
     * Detail periode.
     */
    public function show(Periode $periode)
    {
        $periode->load([
            'pembuat',
            'siswa.kelas.jurusan',
            'tksiBatches',
            'pemeriksaanBerkala',
            'kunjunganKlinik',
        ]);

        return Inertia::render('Admin/Periode/Show', [
            'periode' => $periode,
        ]);
    }


    /**
     * Form edit periode.
     */
    public function edit(Periode $periode)
    {
        $periode->load('siswa');

        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return Inertia::render('Admin/Periode/Edit', [
            'periode' => $periode,
            'siswas' => $siswas,
        ]);
    }


    /**
     * Update periode.
     */
    public function update(
        Request $request,
        Periode $periode
    ) {
        $validated = $request->validate([
            'nama_periode' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'status' => [
    'required',
    'in:aktif,selesai,draft',
],

            'siswa_ids' => [
                'nullable',
                'array',
            ],

            'siswa_ids.*' => [
                'integer',
                'exists:siswas,id',
            ],
        ]);


        DB::transaction(function () use (
            $validated,
            $periode
        ) {

            $periode->update([
                'nama_periode' => $validated['nama_periode'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'status' => $validated['status'],
            ]);


            $periode->siswa()->sync(
                $validated['siswa_ids'] ?? []
            );
        });


        return redirect()
            ->route('admin.periode.index')
            ->with(
                'success',
                'Periode berhasil diperbarui.'
            );
    }


    /**
     * Hapus periode.
     */
    public function destroy(Periode $periode)
    {
        try {

            DB::transaction(function () use ($periode) {

                $periode->siswa()->detach();

                $periode->delete();
            });


            return redirect()
                ->route('admin.periode.index')
                ->with(
                    'success',
                    'Periode berhasil dihapus.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Periode tidak dapat dihapus karena masih digunakan oleh data lain.'
                );
        }
    }
    /**
 * Menampilkan report.
 */
public function report()
    {
        $periodes = Periode::with([
            'siswa',
            'tksiBatches.peserta',
        ])
        ->withCount([
            'siswa as jumlah_siswa',
            'kunjunganKlinik as jumlah_kunjungan',
        ])
        ->orderByDesc('tanggal_mulai')
        ->get();

        $reports = $periodes->map(function ($periode) {

            $jumlahSiswa = $periode->jumlah_siswa;

            /*
             * ==========================================
             * BERKALA 1
             * ==========================================
             */

            $b1Selesai = $periode->pemeriksaanBerkala()
                ->where('jenis_pemeriksaan', 'berkala_1')
                ->whereIn(
                    'siswa_id',
                    $periode->siswa->pluck('id')
                )
                ->distinct()
                ->count('siswa_id');


            /*
             * ==========================================
             * BERKALA 2
             * ==========================================
             */

            $b2Selesai = $periode->pemeriksaanBerkala()
                ->where('jenis_pemeriksaan', 'berkala_2')
                ->whereIn(
                    'siswa_id',
                    $periode->siswa->pluck('id')
                )
                ->distinct()
                ->count('siswa_id');


            /*
             * ==========================================
             * TKSI
             *
             * Periode
             *    ↓
             * TksiBatch
             *    ↓
             * TksiBatchSiswa
             * ==========================================
             */

            $tksiSelesai = $periode->tksiBatches
                ->flatMap(function ($batch) {
                    return $batch->peserta;
                })
                ->whereIn(
                    'siswa_id',
                    $periode->siswa->pluck('id')
                )
                ->where('status', 'selesai')
                ->pluck('siswa_id')
                ->unique()
                ->count();


            return [
                'id' => $periode->id,

                'nama_periode' => $periode->nama_periode,

                'jumlah_siswa' => $jumlahSiswa,

                'jumlah_kunjungan' => $periode->jumlah_kunjungan,

                'berkala_1' => [
                    'selesai' => $b1Selesai,
                    'total' => $jumlahSiswa,
                    'lengkap' =>
                        $jumlahSiswa > 0 &&
                        $b1Selesai >= $jumlahSiswa,
                ],

                'berkala_2' => [
                    'selesai' => $b2Selesai,
                    'total' => $jumlahSiswa,
                    'lengkap' =>
                        $jumlahSiswa > 0 &&
                        $b2Selesai >= $jumlahSiswa,
                ],

                'tksi' => [
                    'selesai' => $tksiSelesai,
                    'total' => $jumlahSiswa,
                    'lengkap' =>
                        $jumlahSiswa > 0 &&
                        $tksiSelesai >= $jumlahSiswa,
                ],
            ];
        });

        return Inertia::render('Admin/Periode/Report', [
            'reports' => $reports,
        ]);
    }
public function showReport(Periode $periode)
{
    $periode->load([
        'siswa.kelas',
    ]);

    $siswaReports = $periode->siswa->map(function ($siswa) use ($periode) {

        // =====================================================
        // B1
        // =====================================================

        $b1 = PemeriksaanBerkala::where('periode_id', $periode->id)
            ->where('siswa_id', $siswa->id)
            ->where(function ($query) {
                $query->where('jenis_pemeriksaan', 'B1')
                    ->orWhere('jenis_pemeriksaan', 'b1')
                    ->orWhere('jenis_pemeriksaan', 'berkala_1')
                    ->orWhere('jenis_pemeriksaan', 'Berkala 1')
                    ->orWhere('jenis_pemeriksaan', 'Pemeriksaan Berkala 1');
            })
            ->latest('tanggal_pemeriksaan')
            ->first();

        // =====================================================
        // B2
        // =====================================================

        $b2 = PemeriksaanBerkala::where('periode_id', $periode->id)
            ->where('siswa_id', $siswa->id)
            ->where(function ($query) {
                $query->where('jenis_pemeriksaan', 'B2')
                    ->orWhere('jenis_pemeriksaan', 'b2')
                    ->orWhere('jenis_pemeriksaan', 'berkala_2')
                    ->orWhere('jenis_pemeriksaan', 'Berkala 2')
                    ->orWhere('jenis_pemeriksaan', 'Pemeriksaan Berkala 2');
            })
            ->latest('tanggal_pemeriksaan')
            ->first();

        // =====================================================
        // KUNJUNGAN
        // =====================================================

        $jumlahKunjungan = KunjunganKlinik::where('periode_id', $periode->id)
            ->where('siswa_id', $siswa->id)
            ->count();

        // =====================================================
        // TKSI
        // =====================================================

        $tksiPeserta = TksiBatchSiswa::with([
                'hasil',
                'batch',
            ])
            ->where('siswa_id', $siswa->id)
            ->whereHas('batch', function ($query) use ($periode) {
                $query->where('periode_id', $periode->id);
            })
            ->latest()
            ->first();

        // =====================================================
        // HASIL TKSI
        // =====================================================

        $tksiHasil = [];

        if ($tksiPeserta) {
            $tksiHasil = $tksiPeserta->hasil->map(function ($hasil) {
                return [
                    'komponen' => $hasil->komponen,
                    'nilai' => $hasil->nilai,
                    'catatan' => $hasil->catatan,
                ];
            })->values();
        }

        // =====================================================
        // STATUS
        // =====================================================

        $b1Lengkap = $b1 !== null;
        $b2Lengkap = $b2 !== null;

        $tksiLengkap = $tksiPeserta !== null
            && $tksiPeserta->hasil->count() > 0;

        $lengkap = $b1Lengkap
            && $b2Lengkap
            && $tksiLengkap;

        // =====================================================
        // HASIL B1
        // =====================================================

        $hasilB1 = $this->formatHasilPemeriksaan($b1);

        // =====================================================
        // HASIL B2
        // =====================================================

        $hasilB2 = $this->formatHasilPemeriksaan($b2);

        return [
            'id' => $siswa->id,
            'nama' => $siswa->nama,
            'nisn' => $siswa->nisn,

            'kelas' => $siswa->kelas?->nama_kelas ?? '-',

            'jumlah_kunjungan' => $jumlahKunjungan,

            'b1' => [
                'lengkap' => $b1Lengkap,
                'tanggal' => $b1?->tanggal_pemeriksaan?->format('d M Y'),
                'hasil' => $hasilB1,
            ],

            'b2' => [
                'lengkap' => $b2Lengkap,
                'tanggal' => $b2?->tanggal_pemeriksaan?->format('d M Y'),
                'hasil' => $hasilB2,
            ],

            'tksi' => [
                'lengkap' => $tksiLengkap,
                'tanggal' => $tksiPeserta?->batch?->tanggal?->format('d M Y'),
                'hasil' => $tksiHasil,
            ],

            'status' => $lengkap
                ? 'Lengkap'
                : 'Belum Lengkap',
        ];
    })->values();

    // =====================================================
    // SUMMARY
    // =====================================================

    $totalSiswa = $siswaReports->count();

    $totalKunjungan = KunjunganKlinik::where(
        'periode_id',
        $periode->id
    )->count();

    $totalLengkap = $siswaReports
        ->where('status', 'Lengkap')
        ->count();

    return Inertia::render('Admin/Periode/ShowReport', [

        'periode' => [
            'id' => $periode->id,
            'nama_periode' => $periode->nama_periode,

            'tanggal_mulai' => $periode->tanggal_mulai
                ? $periode->tanggal_mulai->format('d F Y')
                : '-',

            'tanggal_selesai' => $periode->tanggal_selesai
                ? $periode->tanggal_selesai->format('d F Y')
                : '-',
        ],

        'summary' => [
            'total_siswa' => $totalSiswa,
            'total_kunjungan' => $totalKunjungan,
            'total_lengkap' => $totalLengkap,
        ],

        'siswa' => $siswaReports,
    ]);
}
/**
 * Format hasil pemeriksaan berkala
 */
private function formatHasilPemeriksaan($pemeriksaan)
{
    if (!$pemeriksaan) {
        return [];
    }

    $hasil = $pemeriksaan->hasil;

    if (is_array($hasil)) {
        return $hasil;
    }

    if (is_string($hasil)) {
        $decoded = json_decode($hasil, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return [
            'hasil' => $hasil,
        ];
    }

    return [];
}

}