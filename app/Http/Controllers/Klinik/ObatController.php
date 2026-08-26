<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\ObatBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ObatController extends Controller
{
    /**
     * ============================================================
     * DAFTAR OBAT
     * ============================================================
     */
    public function index()
    {
        $obats = Obat::query()
            ->with([
                'batches' => function ($query) {
                    $query
                        ->orderBy('tanggal_kadaluarsa')
                        ->orderBy('id');
                },
            ])
            ->orderBy('nama_obat')
            ->get()
            ->map(function ($obat) {

                /*
                |------------------------------------------------------
                | TOTAL STOK DARI SEMUA BATCH
                |------------------------------------------------------
                */

                $totalStok = $obat->batches->sum('stok');

                /*
                |------------------------------------------------------
                | JUMLAH BATCH
                |------------------------------------------------------
                */

                $jumlahBatch = $obat->batches->count();

                /*
                |------------------------------------------------------
                | BATCH YANG BELUM EXPIRED
                |------------------------------------------------------
                */

                $batchAktif = $obat->batches
                    ->filter(function ($batch) {
                        return $batch->tanggal_kadaluarsa
                            && $batch->tanggal_kadaluarsa->isToday() === false
                            && $batch->tanggal_kadaluarsa->isFuture()
                            && $batch->stok > 0;
                    })
                    ->sortBy('tanggal_kadaluarsa')
                    ->first();

                return [

                    'id' =>
                        $obat->id,

                    'nama_obat' =>
                        $obat->nama_obat,

                    'satuan' =>
                        $obat->satuan,

                    'keterangan' =>
                        $obat->keterangan,

                    'stok' =>
                        $totalStok,

                    'jumlah_batch' =>
                        $jumlahBatch,

                    /*
                    |--------------------------------------------------
                    | BATCH TERDEKAT EXPIRED
                    |--------------------------------------------------
                    */

                    'batch_terdekat' =>
                        $batchAktif
                            ? [
                                'id' =>
                                    $batchAktif->id,

                                'tanggal_masuk' =>
                                    $batchAktif
                                        ->tanggal_masuk
                                        ->format('Y-m-d'),

                                'tanggal_kadaluarsa' =>
                                    $batchAktif
                                        ->tanggal_kadaluarsa
                                        ->format('Y-m-d'),

                                'jumlah' =>
                                    $batchAktif->jumlah,

                                'stok' =>
                                    $batchAktif->stok,
                            ]
                            : null,

                    /*
                    |--------------------------------------------------
                    | SEMUA BATCH
                    |--------------------------------------------------
                    */

                    'batches' =>
                        $obat->batches
                            ->map(function ($batch) {

                                return [

                                    'id' =>
                                        $batch->id,

                                    'tanggal_masuk' =>
                                        $batch
                                            ->tanggal_masuk
                                            ->format('Y-m-d'),

                                    'tanggal_kadaluarsa' =>
                                        $batch
                                            ->tanggal_kadaluarsa
                                            ->format('Y-m-d'),

                                    'jumlah' =>
                                        $batch->jumlah,

                                    'stok' =>
                                        $batch->stok,
                                ];
                            })
                            ->values(),
                ];
            })
            ->values();

        return Inertia::render(
            'Klinik/Obat/Index',
            [
                'obats' => $obats,
            ]
        );
    }


    /**
     * ============================================================
     * FORM TAMBAH OBAT
     * ============================================================
     */
    public function create()
    {
        return Inertia::render(
            'Klinik/Obat/Create'
        );
    }


    /**
     * ============================================================
     * SIMPAN OBAT + BATCH PERTAMA
     * ============================================================
     *
     * Pada tahap ini:
     *
     * 1. Nama obat harus unik
     * 2. Obat dibuat
     * 3. Batch pertama langsung dibuat
     *
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------
        */

        $validated = $request->validate([

            'nama_obat' => [
                'required',
                'string',
                'max:255',
                'unique:obats,nama_obat',
            ],

            'satuan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            /*
            |----------------------------------------------------------
            | BATCH PERTAMA
            |----------------------------------------------------------
            */

            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'tanggal_kadaluarsa' => [
                'required',
                'date',
                'after_or_equal:tanggal_masuk',
            ],

            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [

            'nama_obat.required' =>
                'Nama obat wajib diisi.',

            'nama_obat.unique' =>
                'Obat dengan nama tersebut sudah terdaftar. Silakan tambahkan batch baru.',

            'tanggal_masuk.required' =>
                'Tanggal masuk wajib diisi.',

            'tanggal_kadaluarsa.required' =>
                'Tanggal kadaluarsa wajib diisi.',

            'tanggal_kadaluarsa.after_or_equal' =>
                'Tanggal kadaluarsa tidak boleh sebelum tanggal masuk.',

            'jumlah.required' =>
                'Jumlah obat wajib diisi.',

            'jumlah.integer' =>
                'Jumlah obat harus berupa angka.',

            'jumlah.min' =>
                'Jumlah obat minimal 1.',
        ]);


        /*
        |--------------------------------------------------------------
        | TRANSAKSI
        |--------------------------------------------------------------
        */

        DB::transaction(function () use ($validated) {

            /*
            |----------------------------------------------------------
            | BUAT OBAT UTAMA
            |----------------------------------------------------------
            */

            $obat = Obat::create([

                'nama_obat' =>
                    $validated['nama_obat'],

                'satuan' =>
                    $validated['satuan'] ?? null,

                'keterangan' =>
                    $validated['keterangan'] ?? null,
            ]);


            /*
            |----------------------------------------------------------
            | BUAT BATCH PERTAMA
            |----------------------------------------------------------
            */

            ObatBatch::create([

                'obat_id' =>
                    $obat->id,

                'tanggal_masuk' =>
                    $validated['tanggal_masuk'],

                'tanggal_kadaluarsa' =>
                    $validated['tanggal_kadaluarsa'],

                'jumlah' =>
                    $validated['jumlah'],

                /*
                | Stok awal = jumlah masuk
                */

                'stok' =>
                    $validated['jumlah'],
            ]);
        });


        /*
        |--------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------
        */

        return redirect()
            ->route('klinik.obat.index')
            ->with(
                'success',
                'Obat dan batch pertama berhasil ditambahkan.'
            );
    }


    /**
     * ============================================================
     * FORM TAMBAH BATCH
     * ============================================================
     *
     * Contoh:
     *
     * Paracetamol sudah ada.
     *
     * User tidak membuat Paracetamol lagi.
     *
     * User masuk:
     *
     * Tambah Batch
     *
     */
    public function createBatch(Obat $obat)
    {
        return Inertia::render(
            'Klinik/Obat/Batch/Create',
            [
                'obat' => [
                    'id' =>
                        $obat->id,

                    'nama_obat' =>
                        $obat->nama_obat,

                    'satuan' =>
                        $obat->satuan,
                ],
            ]
        );
    }


    /**
     * ============================================================
     * SIMPAN BATCH BARU
     * ============================================================
     */
    public function storeBatch(
        Request $request,
        Obat $obat
    ) {

        /*
        |--------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------
        */

        $validated = $request->validate([

            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'tanggal_kadaluarsa' => [
                'required',
                'date',
                'after_or_equal:tanggal_masuk',
            ],

            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

        ], [

            'tanggal_masuk.required' =>
                'Tanggal masuk wajib diisi.',

            'tanggal_kadaluarsa.required' =>
                'Tanggal kadaluarsa wajib diisi.',

            'tanggal_kadaluarsa.after_or_equal' =>
                'Tanggal kadaluarsa tidak boleh sebelum tanggal masuk.',

            'jumlah.required' =>
                'Jumlah obat wajib diisi.',

            'jumlah.integer' =>
                'Jumlah obat harus berupa angka.',

            'jumlah.min' =>
                'Jumlah obat minimal 1.',
        ]);


        /*
        |--------------------------------------------------------------
        | SIMPAN BATCH
        |--------------------------------------------------------------
        */

        ObatBatch::create([

            'obat_id' =>
                $obat->id,

            'tanggal_masuk' =>
                $validated['tanggal_masuk'],

            'tanggal_kadaluarsa' =>
                $validated['tanggal_kadaluarsa'],

            'jumlah' =>
                $validated['jumlah'],

            'stok' =>
                $validated['jumlah'],
        ]);


        /*
        |--------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------
        */

        return redirect()
            ->route('klinik.obat.index')
            ->with(
                'success',
                'Batch obat berhasil ditambahkan.'
            );
    }


    /**
     * ============================================================
     * EDIT OBAT
     * ============================================================
     *
     * Yang diedit hanya informasi obat utama.
     *
     * Batch dikelola terpisah.
     *
     */
    public function edit(Obat $obat)
{
    $obat->load([
        'batches' => function ($query) {
            $query
                ->orderByDesc('tanggal_masuk')
                ->orderByDesc('id');
        },
    ]);

    return Inertia::render(
        'Klinik/Obat/Edit',
        [
            'obat' => [
                'id' =>
                    $obat->id,

                'nama_obat' =>
                    $obat->nama_obat,

                'satuan' =>
                    $obat->satuan,

                'keterangan' =>
                    $obat->keterangan,

                'stok' =>
                    $obat->batches->sum('stok'),

                'jumlah_batch' =>
                    $obat->batches->count(),

                'batches' =>
                    $obat->batches
                        ->map(function ($batch) {

                            return [
                                'id' =>
                                    $batch->id,

                                'tanggal_masuk' =>
                                    $batch->tanggal_masuk
                                        ? $batch->tanggal_masuk->format('Y-m-d')
                                        : null,

                                'tanggal_kadaluarsa' =>
                                    $batch->tanggal_kadaluarsa
                                        ? $batch->tanggal_kadaluarsa->format('Y-m-d')
                                        : null,

                                'jumlah' =>
                                    $batch->jumlah,

                                'stok' =>
                                    $batch->stok,
                            ];
                        })
                        ->values(),
            ],
        ]
    );
}

    /**
     * ============================================================
     * UPDATE OBAT
     * ============================================================
     */
    public function update(
        Request $request,
        Obat $obat
    ) {

        /*
        |--------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------
        */

        $validated = $request->validate([

            'nama_obat' => [
                'required',
                'string',
                'max:255',

                /*
                | Nama boleh tetap sama untuk obat yang sedang diedit,
                | tetapi tidak boleh sama dengan obat lain.
                */

                Rule::unique('obats', 'nama_obat')
                    ->ignore($obat->id),
            ],

            'satuan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

        ], [

            'nama_obat.required' =>
                'Nama obat wajib diisi.',

            'nama_obat.unique' =>
                'Nama obat tersebut sudah digunakan oleh obat lain.',
        ]);


        /*
        |--------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------
        */

        $obat->update([

            'nama_obat' =>
                $validated['nama_obat'],

            'satuan' =>
                $validated['satuan'] ?? null,

            'keterangan' =>
                $validated['keterangan'] ?? null,
        ]);


        return redirect()
            ->route('klinik.obat.index')
            ->with(
                'success',
                'Data obat berhasil diperbarui.'
            );
    }


    /**
     * ============================================================
     * DELETE OBAT
     * ============================================================
     */
    public function destroy(Obat $obat)
    {
        try {

            /*
            |----------------------------------------------------------
            | OBAT DIHAPUS BERSAMA BATCH
            |----------------------------------------------------------
            |
            | Karena foreign key obat_batches.obat_id menggunakan
            | cascadeOnDelete().
            |
            */

            $obat->delete();

            return redirect()
                ->route('klinik.obat.index')
                ->with(
                    'success',
                    'Data obat berhasil dihapus.'
                );

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