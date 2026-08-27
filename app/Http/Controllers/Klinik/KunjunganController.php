<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\KunjunganObat;
use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\Penyakit;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    /**
     * ============================================================
     * DAFTAR KUNJUNGAN KLINIK
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = KunjunganKlinik::query()
            ->with([
                'siswa.kelas.jurusan',
                'pemeriksa',
                'periode',
                'penyakit',
                'kunjunganObat.obat',
                'kunjunganObat.batch',
            ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH SISWA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas('siswa', function ($q) use ($search) {

                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER PERIODE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('periode_id')) {

            $query->where(
                'periode_id',
                $request->periode_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('tanggal')) {

            $query->whereDate(
                'created_at',
                $request->tanggal
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $kunjungan = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($item) {

                return [

                    'id' => $item->id,

                    'created_at' => $item->created_at
                        ? $item->created_at->format('d/m/Y H:i')
                        : null,

                    'tanggal' => $item->created_at
                        ? $item->created_at->format('Y-m-d')
                        : null,

                    'updated_at' => $item->updated_at
                        ? $item->updated_at->format('d/m/Y H:i')
                        : null,

                    'tanggal_kunjungan' =>
                        $item->tanggal_kunjungan
                            ? Carbon::parse(
                                $item->tanggal_kunjungan
                            )->format('d/m/Y H:i')
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | KESEHATAN
                    |--------------------------------------------------------------------------
                    */

                    'keluhan' =>
                        $item->keluhan,

                    'pemeriksaan' =>
                        $item->pemeriksaan,

                    /*
                    |--------------------------------------------------------------------------
                    | PENYAKIT
                    |--------------------------------------------------------------------------
                    */

                    'penyakit' => $item->penyakit
                        ? [
                            'id' =>
                                $item->penyakit->id,

                            'nama_penyakit' =>
                                $item->penyakit->nama_penyakit,

                            'kategori' =>
                                $item->penyakit->kategori,

                            'keterangan' =>
                                $item->penyakit->keterangan,
                        ]
                        : null,

                    'diagnosis' =>
                        $item->penyakit?->nama_penyakit,

                    /*
                    |--------------------------------------------------------------------------
                    | TRIASE
                    |--------------------------------------------------------------------------
                    */

                    'triase' =>
                        $item->triase,

                    /*
                    |--------------------------------------------------------------------------
                    | TINDAKAN
                    |--------------------------------------------------------------------------
                    */

                    'tindakan' =>
                        $item->tindakan,

                    /*
                    |--------------------------------------------------------------------------
                    | CATATAN
                    |--------------------------------------------------------------------------
                    */

                    'catatan' =>
                        $item->catatan,

                    /*
                    |--------------------------------------------------------------------------
                    | SISWA
                    |--------------------------------------------------------------------------
                    */

                    'siswa' => $item->siswa
                        ? [

                            'id' =>
                                $item->siswa->id,

                            'nisn' =>
                                $item->siswa->nisn,

                            'nama' =>
                                $item->siswa->nama,

                            'kelas' =>
                                $item->siswa->kelas
                                    ? [

                                        'id' =>
                                            $item->siswa
                                                ->kelas
                                                ->id,

                                        'nama_kelas' =>
                                            $item->siswa
                                                ->kelas
                                                ->nama_kelas,

                                    ]
                                    : null,

                            'jurusan' =>
                                $item->siswa->kelas?->jurusan
                                    ? [

                                        'id' =>
                                            $item->siswa
                                                ->kelas
                                                ->jurusan
                                                ->id,

                                        'nama_jurusan' =>
                                            $item->siswa
                                                ->kelas
                                                ->jurusan
                                                ->nama_jurusan,

                                    ]
                                    : null,
                        ]
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | PEMERIKSA
                    |--------------------------------------------------------------------------
                    */

                    'pemeriksa' =>
                        $item->pemeriksa
                            ? [

                                'id' =>
                                    $item->pemeriksa->id,

                                'name' =>
                                    $item->pemeriksa->name,

                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | PERIODE
                    |--------------------------------------------------------------------------
                    */

                    'periode' =>
                        $item->periode
                            ? [

                                'id' =>
                                    $item->periode->id,

                                'nama_periode' =>
                                    $item->periode->nama_periode,

                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | OBAT + BATCH
                    |--------------------------------------------------------------------------
                    */

                    'obat' =>
                        $item->kunjunganObat
                            ->map(function ($itemObat) {

                                return [

                                    'id' =>
                                        $itemObat->id,

                                    'obat_id' =>
                                        $itemObat->obat_id,

                                    'batch_id' =>
                                        $itemObat->batch_id,

                                    'nama_obat' =>
                                        $itemObat
                                            ->obat
                                            ?->nama_obat,

                                    'satuan' =>
                                        $itemObat
                                            ->obat
                                            ?->satuan,

                                    'kode_batch' =>
                                        $itemObat
                                            ->batch
                                            ?->kode_batch,

                                    'tanggal_masuk' =>
                                        $itemObat
                                            ->batch
                                            ?->tanggal_masuk
                                            ?->format('Y-m-d'),

                                    'tanggal_kadaluarsa' =>
                                        $itemObat
                                            ->batch
                                            ?->tanggal_kadaluarsa
                                            ?->format('Y-m-d'),

                                    'keterangan_exp' =>
                                        $itemObat->batch
                                            ?->tanggal_kadaluarsa
                                            ?->format('d/m/Y'),

                                    'jumlah' =>
                                        $itemObat->jumlah,

                                    'keterangan' =>
                                        $itemObat->keterangan,
                                ];
                            })
                            ->values(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | DATA PERIODE
        |--------------------------------------------------------------------------
        */

        $periodeList = Periode::query()
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->map(function ($periode) {

                return [

                    'id' =>
                        $periode->id,

                    'nama_periode' =>
                        $periode->nama_periode,

                    'tanggal_mulai' =>
                        $periode->tanggal_mulai,

                    'tanggal_selesai' =>
                        $periode->tanggal_selesai,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $totalKunjungan =
            KunjunganKlinik::count();

        $kunjunganHariIni =
            KunjunganKlinik::whereDate(
                'created_at',
                today()
            )->count();

        /*
        |--------------------------------------------------------------------------
        | TREND PENYAKIT
        |--------------------------------------------------------------------------
        */

        $trendPenyakitQuery = KunjunganKlinik::query()
            ->select(
                'penyakit_id',
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereNotNull('penyakit_id')
            ->groupBy('penyakit_id')
            ->with('penyakit');

        if ($request->filled('periode_id')) {

            $trendPenyakitQuery->where(
                'periode_id',
                $request->periode_id
            );
        }

        $trendPenyakit = $trendPenyakitQuery
            ->get()
            ->map(function ($item) {

                return [

                    'id' =>
                        $item->penyakit_id,

                    'nama_penyakit' =>
                        $item->penyakit?->nama_penyakit
                        ?? 'Tidak diketahui',

                    'jumlah' =>
                        (int) $item->jumlah,
                ];
            })
            ->sortByDesc('jumlah')
            ->values();

        $maxTrendPenyakit =
            $trendPenyakit->max('jumlah') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | TREND TRIASE
        |--------------------------------------------------------------------------
        */

        $trendTriaseQuery = KunjunganKlinik::query()
            ->select(
                'triase',
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereNotNull('triase')
            ->groupBy('triase');

        if ($request->filled('periode_id')) {

            $trendTriaseQuery->where(
                'periode_id',
                $request->periode_id
            );
        }

        $trendTriase = $trendTriaseQuery
            ->get()
            ->map(function ($item) {

                return [

                    'triase' =>
                        $item->triase,

                    'jumlah' =>
                        (int) $item->jumlah,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Index',
            [

                'kunjungan' =>
                    $kunjungan,

                'periodeList' =>
                    $periodeList,

                'statistik' => [

                    'total' =>
                        $totalKunjungan,

                    'hari_ini' =>
                        $kunjunganHariIni,

                    'trend_penyakit' =>
                        $trendPenyakit,

                    'max_trend_penyakit' =>
                        $maxTrendPenyakit,

                    'trend_triase' =>
                        $trendTriase,
                ],

                'filter' => [

                    'search' =>
                        $request->search,

                    'periode_id' =>
                        $request->periode_id,

                    'tanggal' =>
                        $request->tanggal,
                ],
            ]
        );
    }


    /**
     * ============================================================
     * FORM INPUT KUNJUNGAN
     * ============================================================
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        $periode = Periode::where(
            'status',
            'aktif'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | SISWA
        |--------------------------------------------------------------------------
        */

        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) {

                return [

                    'id' =>
                        $siswa->id,

                    'nisn' =>
                        $siswa->nisn,

                    'nama' =>
                        $siswa->nama,

                    'kelas' =>
                        $siswa->kelas
                            ? [

                                'id' =>
                                    $siswa->kelas->id,

                                'nama_kelas' =>
                                    $siswa->kelas->nama_kelas,

                            ]
                            : null,

                    'jurusan' =>
                        $siswa->kelas?->jurusan
                            ? [

                                'id' =>
                                    $siswa
                                        ->kelas
                                        ->jurusan
                                        ->id,

                                'nama_jurusan' =>
                                    $siswa
                                        ->kelas
                                        ->jurusan
                                        ->nama_jurusan,

                            ]
                            : null,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | PENYAKIT
        |--------------------------------------------------------------------------
        */

        $penyakitList = Penyakit::query()
            ->orderBy('nama_penyakit')
            ->get()
            ->map(function ($penyakit) {

                return [

                    'id' =>
                        $penyakit->id,

                    'nama_penyakit' =>
                        $penyakit->nama_penyakit,

                    'kategori' =>
                        $penyakit->kategori,

                    'keterangan' =>
                        $penyakit->keterangan,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | OBAT + BATCH
        |--------------------------------------------------------------------------
        |
        | ATURAN:
        |
        | 1. Stok harus > 0
        | 2. Batch tidak boleh expired
        | 3. Batch H-1 expired sudah tidak boleh dipilih
        | 4. Batch diurutkan dari expiry terdekat
        | 5. Batch pertama otomatis menjadi batch terdekat
        |
        */

        $batasKadaluarsa =
            today()->addDay();

        $obatList = Obat::query()
            ->with([
                'batches' => function ($query) use ($batasKadaluarsa) {

                    $query
                        ->where('stok', '>', 0)

                        /*
                        | Batch yang exp besok sudah tidak boleh dipilih.
                        |
                        | Contoh:
                        | Hari ini 25
                        | Exp 26 -> tidak muncul
                        | Exp 27 -> muncul
                        */
                        ->whereDate(
                            'tanggal_kadaluarsa',
                            '>',
                            $batasKadaluarsa
                        )

                        ->orderBy(
                            'tanggal_kadaluarsa',
                            'asc'
                        )

                        ->orderBy(
                            'tanggal_masuk',
                            'asc'
                        )

                        ->orderBy(
                            'id',
                            'asc'
                        );
                }
            ])
            ->orderBy('nama_obat')
            ->get()
            ->map(function ($obat) {

                /*
                |--------------------------------------------------------------------------
                | BATCH TERDEKAT
                |--------------------------------------------------------------------------
                */

                $batchTerdekat =
                    $obat->batches->first();

                return [

                    'id' =>
                        $obat->id,

                    'nama_obat' =>
                        $obat->nama_obat,

                    'satuan' =>
                        $obat->satuan,

                    'keterangan' =>
                        $obat->keterangan,

                    'total_stok' =>
                        (int) $obat->batches->sum('stok'),

                    /*
                    |--------------------------------------------------------------------------
                    | BATCH TERDEKAT
                    |--------------------------------------------------------------------------
                    */

                    'batch_terdekat' =>
                        $batchTerdekat
                            ? [

                                'id' =>
                                    $batchTerdekat->id,

                                'kode_batch' =>
                                    $batchTerdekat->kode_batch,

                                'tanggal_kadaluarsa' =>
                                    $batchTerdekat
                                        ->tanggal_kadaluarsa
                                        ->format('Y-m-d'),

                                'keterangan_exp' =>
                                    'Exp. ' .
                                    $batchTerdekat
                                        ->tanggal_kadaluarsa
                                        ->format('d/m/Y'),

                                'stok' =>
                                    (int) $batchTerdekat->stok,

                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | SEMUA BATCH YANG MASIH BISA DIPILIH
                    |--------------------------------------------------------------------------
                    */

                    'batches' =>
                        $obat->batches
                            ->values()
                            ->map(function ($batch, $index) {

                                return [

                                    'id' =>
                                        $batch->id,

                                    'kode_batch' =>
                                        $batch->kode_batch,

                                    'tanggal_masuk' =>
                                        $batch->tanggal_masuk
                                            ? $batch
                                                ->tanggal_masuk
                                                ->format('Y-m-d')
                                            : null,

                                    'tanggal_kadaluarsa' =>
                                        $batch->tanggal_kadaluarsa
                                            ? $batch
                                                ->tanggal_kadaluarsa
                                                ->format('Y-m-d')
                                            : null,

                                    /*
                                    | KETERANGAN EXP
                                    */

                                    'keterangan_exp' =>
                                        $batch->tanggal_kadaluarsa
                                            ? 'Exp. ' .
                                                $batch
                                                    ->tanggal_kadaluarsa
                                                    ->format('d/m/Y')
                                            : null,

                                    /*
                                    | BATCH PERTAMA =
                                    | BATCH EXPIRED TERDEKAT
                                    */

                                    'is_terdekat' =>
                                        $index === 0,

                                    'stok' =>
                                        (int) $batch->stok,

                                    'keterangan' =>
                                        $batch->keterangan,
                                ];
                            })
                            ->values(),
                ];
            })
            /*
            |--------------------------------------------------------------------------
            | HANYA OBAT YANG MASIH PUNYA BATCH AKTIF
            |--------------------------------------------------------------------------
            */

            ->filter(function ($obat) {

                return $obat['batches']->isNotEmpty();
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TRIASE
        |--------------------------------------------------------------------------
        */

        $triaseList =
            $this->triaseList();

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Create',
            [

                'periode' =>
                    $periode
                        ? [

                            'id' =>
                                $periode->id,

                            'nama_periode' =>
                                $periode->nama_periode,

                        ]
                        : null,

                'siswas' =>
                    $siswas,

                'penyakitList' =>
                    $penyakitList,

                'obatList' =>
                    $obatList,

                'triaseList' =>
                    $triaseList,
            ]
        );
    }


    /**
     * ============================================================
     * SIMPAN KUNJUNGAN
     * ============================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'periode_id' => [
                'required',
                'exists:periodes,id',
            ],

            'siswa_id' => [
                'required',
                'exists:siswas,id',
            ],

            'keluhan' => [
                'nullable',
                'string',
            ],

            'pemeriksaan' => [
                'nullable',
                'string',
            ],

            'penyakit_id' => [
                'nullable',
                'exists:penyakits,id',
            ],

            'triase' => [
                'required',
                'in:merah,kuning,hijau,hitam',
            ],

            'tindakan' => [
                'nullable',
                'string',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],

            'obat' => [
                'nullable',
                'array',
            ],

            'obat.*.obat_id' => [
                'required',
                'exists:obats,id',
            ],

            'obat.*.batch_id' => [
                'nullable',
                'exists:obat_batches,id',
            ],

            'obat.*.jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

            'obat.*.keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $kunjungan = DB::transaction(function () use ($validated) {

    $kunjungan = KunjunganKlinik::create([
        'periode_id' => $validated['periode_id'],
        'tanggal_kunjungan' => now(),
        'siswa_id' => $validated['siswa_id'],
        'keluhan' => $validated['keluhan'] ?? null,
        'pemeriksaan' => $validated['pemeriksaan'] ?? null,
        'penyakit_id' => $validated['penyakit_id'] ?? null,
        'triase' => $validated['triase'],
        'tindakan' => $validated['tindakan'] ?? null,
        'catatan' => $validated['catatan'] ?? null,
        'pemeriksa_id' => auth()->id(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | PROSES OBAT
    |--------------------------------------------------------------------------
    */

    if (!empty($validated['obat'])) {

        foreach ($validated['obat'] as $item) {

            $this->kurangiStokObat(
                $kunjungan,
                $item
            );
        }
    }

    return $kunjungan;
});


/*
|--------------------------------------------------------------------------
| LOAD SISWA
|--------------------------------------------------------------------------
*/

$kunjungan->load('siswa');


/*
|--------------------------------------------------------------------------
| NOTIFIKASI ADMIN
|--------------------------------------------------------------------------
*/

NotificationService::toRole(
    'admin',
    'Kunjungan Klinik Baru',
    "{$kunjungan->siswa->nama} baru saja melakukan kunjungan klinik dengan triase {$kunjungan->triase}.",
    $kunjungan->triase === 'merah' || $kunjungan->triase === 'hitam'
        ? 'danger'
        : 'info',
    route('admin.kunjungan.show', $kunjungan)
);


/*
|--------------------------------------------------------------------------
| NOTIFIKASI TKSI
|--------------------------------------------------------------------------
*/

if (
    in_array(
        $kunjungan->triase,
        ['merah', 'kuning', 'hitam'],
        true
    )
) {

    NotificationService::toRole(
        'tksi',
        'Tindak Lanjut Kunjungan Klinik',
        "Siswa {$kunjungan->siswa->nama} memiliki kunjungan klinik dengan triase {$kunjungan->triase}. Mohon ditindaklanjuti.",
        $kunjungan->triase === 'merah' || $kunjungan->triase === 'hitam'
            ? 'danger'
            : 'warning',
        route('tksi.input.index')
    );
}


/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

return redirect()
    ->route('klinik.kesehatan.kunjungan.index')
    ->with(
        'success',
        'Data kunjungan klinik berhasil disimpan.'
    );}


    /**
     * ============================================================
     * DETAIL KUNJUNGAN
     * ============================================================
     */
    public function show(
        KunjunganKlinik $kunjungan
    ) {

        $kunjungan->load([

            'siswa.kelas.jurusan',
            'pemeriksa',
            'periode',
            'penyakit',
            'kunjunganObat.obat',
            'kunjunganObat.batch',
        ]);

        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Show',
            [

                'kunjungan' => [

                    'id' =>
                        $kunjungan->id,

                    'created_at' =>
                        $kunjungan->created_at
                            ? $kunjungan
                                ->created_at
                                ->format('d/m/Y H:i')
                            : null,

                    'tanggal_kunjungan' =>
                        $kunjungan->tanggal_kunjungan
                            ? Carbon::parse(
                                $kunjungan->tanggal_kunjungan
                            )->format('d/m/Y H:i')
                            : (
                                $kunjungan->created_at
                                    ? $kunjungan
                                        ->created_at
                                        ->format('d/m/Y H:i')
                                    : null
                            ),

                    'updated_at' =>
                        $kunjungan->updated_at
                            ? $kunjungan
                                ->updated_at
                                ->format('d/m/Y H:i')
                            : null,

                    'keluhan' =>
                        $kunjungan->keluhan,

                    'pemeriksaan' =>
                        $kunjungan->pemeriksaan,

                    'penyakit' =>
                        $kunjungan->penyakit
                            ? [

                                'id' =>
                                    $kunjungan
                                        ->penyakit
                                        ->id,

                                'nama_penyakit' =>
                                    $kunjungan
                                        ->penyakit
                                        ->nama_penyakit,

                                'kategori' =>
                                    $kunjungan
                                        ->penyakit
                                        ->kategori,

                                'keterangan' =>
                                    $kunjungan
                                        ->penyakit
                                        ->keterangan,

                            ]
                            : null,

                    'diagnosis' =>
                        $kunjungan
                            ->penyakit
                            ?->nama_penyakit,

                    'triase' =>
                        $kunjungan->triase,

                    'tindakan' =>
                        $kunjungan->tindakan,

                    'catatan' =>
                        $kunjungan->catatan,

                    'siswa' =>
                        $kunjungan->siswa
                            ? [

                                'id' =>
                                    $kunjungan
                                        ->siswa
                                        ->id,

                                'nisn' =>
                                    $kunjungan
                                        ->siswa
                                        ->nisn,

                                'nama' =>
                                    $kunjungan
                                        ->siswa
                                        ->nama,

                                'kelas' =>
                                    $kunjungan
                                        ->siswa
                                        ->kelas
                                        ? [

                                            'id' =>
                                                $kunjungan
                                                    ->siswa
                                                    ->kelas
                                                    ->id,

                                            'nama_kelas' =>
                                                $kunjungan
                                                    ->siswa
                                                    ->kelas
                                                    ->nama_kelas,

                                        ]
                                        : null,

                                'jurusan' =>
                                    $kunjungan
                                        ->siswa
                                        ->kelas
                                        ?->jurusan
                                        ? [

                                            'id' =>
                                                $kunjungan
                                                    ->siswa
                                                    ->kelas
                                                    ->jurusan
                                                    ->id,

                                            'nama_jurusan' =>
                                                $kunjungan
                                                    ->siswa
                                                    ->kelas
                                                    ->jurusan
                                                    ->nama_jurusan,

                                        ]
                                        : null,
                            ]
                            : null,

                    'pemeriksa' =>
                        $kunjungan->pemeriksa
                            ? [

                                'id' =>
                                    $kunjungan
                                        ->pemeriksa
                                        ->id,

                                'name' =>
                                    $kunjungan
                                        ->pemeriksa
                                        ->name,

                            ]
                            : null,

                    'periode' =>
                        $kunjungan->periode
                            ? [

                                'id' =>
                                    $kunjungan
                                        ->periode
                                        ->id,

                                'nama_periode' =>
                                    $kunjungan
                                        ->periode
                                        ->nama_periode,

                            ]
                            : null,

                    'obat' =>
                        $kunjungan
                            ->kunjunganObat
                            ->map(function ($item) {

                                return [

                                    'id' =>
                                        $item->id,

                                    'obat_id' =>
                                        $item->obat_id,

                                    'batch_id' =>
                                        $item->batch_id,

                                    'nama_obat' =>
                                        $item
                                            ->obat
                                            ?->nama_obat,

                                    'satuan' =>
                                        $item
                                            ->obat
                                            ?->satuan,

                                    'kode_batch' =>
                                        $item
                                            ->batch
                                            ?->kode_batch,

                                    'tanggal_masuk' =>
                                        $item
                                            ->batch
                                            ?->tanggal_masuk
                                            ?->format('Y-m-d'),

                                    'tanggal_kadaluarsa' =>
                                        $item
                                            ->batch
                                            ?->tanggal_kadaluarsa
                                            ?->format('Y-m-d'),

                                    'keterangan_exp' =>
                                        $item->batch
                                            ?->tanggal_kadaluarsa
                                            ?->format('d/m/Y'),

                                    'jumlah' =>
                                        $item->jumlah,

                                    'keterangan' =>
                                        $item->keterangan,
                                ];
                            })
                            ->values(),
                ],
            ]
        );
    }


    /**
     * ============================================================
     * PRINT
     * ============================================================
     */
    public function print(
        KunjunganKlinik $kunjungan
    ) {

        $kunjungan->load([
            'siswa.kelas.jurusan',
            'pemeriksa',
            'periode',
            'penyakit',
            'kunjunganObat.obat',
            'kunjunganObat.batch',
        ]);

        return view(
            'pdf.kunjungan-klinik',
            [
                'kunjungan' =>
                    $kunjungan,
            ]
        );
    }


    /**
     * ============================================================
     * PDF
     * ============================================================
     */
    public function pdf(
        KunjunganKlinik $kunjungan
    ) {

        $kunjungan->load([
            'siswa.kelas.jurusan',
            'pemeriksa',
            'periode',
            'penyakit',
            'kunjunganObat.obat',
            'kunjunganObat.batch',
        ]);

        $pdf = Pdf::loadView(
            'pdf.kunjungan-klinik',
            [
                'kunjungan' =>
                    $kunjungan,
            ]
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        $namaSiswa =
            $kunjungan
                ->siswa
                ?->nama;

        $namaSiswa = $namaSiswa
            ? preg_replace(
                '/[^A-Za-z0-9\-]/',
                '-',
                $namaSiswa
            )
            : 'siswa';

        $tanggal =
            $kunjungan
                ->created_at
                ? $kunjungan
                    ->created_at
                    ->format('Y-m-d')
                : now()->format('Y-m-d');

        return $pdf->download(
            'kunjungan-klinik-' .
            $namaSiswa .
            '-' .
            $tanggal .
            '.pdf'
        );
    }


    /**
     * ============================================================
     * FORM EDIT
     * ============================================================
     */
    public function edit(
        KunjunganKlinik $kunjungan
    ) {

        $kunjungan->load([

            'siswa.kelas.jurusan',
            'penyakit',
            'periode',
            'kunjunganObat.obat',
            'kunjunganObat.batch',
        ]);

        /*
        |--------------------------------------------------------------------------
        | SISWA
        |--------------------------------------------------------------------------
        */

        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) {

                return [

                    'id' =>
                        $siswa->id,

                    'nisn' =>
                        $siswa->nisn,

                    'nama' =>
                        $siswa->nama,

                    'kelas' =>
                        $siswa->kelas
                            ? [

                                'id' =>
                                    $siswa->kelas->id,

                                'nama_kelas' =>
                                    $siswa->kelas->nama_kelas,

                            ]
                            : null,

                    'jurusan' =>
                        $siswa->kelas?->jurusan
                            ? [

                                'id' =>
                                    $siswa
                                        ->kelas
                                        ->jurusan
                                        ->id,

                                'nama_jurusan' =>
                                    $siswa
                                        ->kelas
                                        ->jurusan
                                        ->nama_jurusan,

                            ]
                            : null,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | PENYAKIT
        |--------------------------------------------------------------------------
        */

        $penyakitList = Penyakit::query()
            ->orderBy('nama_penyakit')
            ->get()
            ->map(function ($penyakit) {

                return [

                    'id' =>
                        $penyakit->id,

                    'nama_penyakit' =>
                        $penyakit->nama_penyakit,

                    'kategori' =>
                        $penyakit->kategori,

                    'keterangan' =>
                        $penyakit->keterangan,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | BATCH YANG SEDANG DIPAKAI PADA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $batchTerpakaiIds =
            $kunjungan
                ->kunjunganObat
                ->pluck('batch_id')
                ->filter()
                ->values()
                ->toArray();

        /*
        |--------------------------------------------------------------------------
        | BATAS MINIMAL EXPIRY
        |--------------------------------------------------------------------------
        */

        $batasKadaluarsa =
            today()->addDay();

        /*
        |--------------------------------------------------------------------------
        | OBAT + BATCH
        |--------------------------------------------------------------------------
        |
        | Pada edit:
        |
        | - Batch aktif tetap ditampilkan.
        | - Batch H-1 yang sedang digunakan tetap ditampilkan
        |   supaya data lama tidak tiba-tiba hilang.
        | - Batch lain yang sudah H-1 tidak ditampilkan.
        |
        */

        $obatList = Obat::query()
            ->with([
                'batches' => function ($query) use (
                    $batasKadaluarsa,
                    $batchTerpakaiIds
                ) {

                    $query->where(function ($q) use (
                        $batasKadaluarsa,
                        $batchTerpakaiIds
                    ) {

                        /*
                        | Batch normal yang masih aman
                        */

                        $q->where(function ($q2) use (
                            $batasKadaluarsa
                        ) {

                            $q2->where(
                                'stok',
                                '>',
                                0
                            )
                            ->whereDate(
                                'tanggal_kadaluarsa',
                                '>',
                                $batasKadaluarsa
                            );
                        });

                        /*
                        | ATAU batch yang sedang digunakan
                        */

                        if (!empty($batchTerpakaiIds)) {

                            $q->orWhereIn(
                                'id',
                                $batchTerpakaiIds
                            );
                        }
                    })

                    ->orderBy(
                        'tanggal_kadaluarsa',
                        'asc'
                    )

                    ->orderBy(
                        'tanggal_masuk',
                        'asc'
                    )

                    ->orderBy(
                        'id',
                        'asc'
                    );
                }
            ])
            ->orderBy('nama_obat')
            ->get()
            ->map(function ($obat) use (
                $batchTerpakaiIds
            ) {

                $batchTerdekat = $obat->batches
                    ->filter(function ($batch) use (
                        $batchTerpakaiIds
                    ) {

                        /*
                        | Batch yang benar-benar masih boleh dipilih.
                        */

                        return
                            $batch->stok > 0
                            &&
                            $batch->tanggal_kadaluarsa
                            &&
                            $batch
                                ->tanggal_kadaluarsa
                                ->gt(
                                    today()->addDay()
                                );
                    })
                    ->sortBy([
                        [
                            'tanggal_kadaluarsa',
                            'asc'
                        ],
                        [
                            'tanggal_masuk',
                            'asc'
                        ],
                    ])
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

                    'total_stok' =>
                        (int) $obat->batches->sum(
                            'stok'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | BATCH TERDEKAT
                    |--------------------------------------------------------------------------
                    */

                    'batch_terdekat' =>
                        $batchTerdekat
                            ? [

                                'id' =>
                                    $batchTerdekat->id,

                                'kode_batch' =>
                                    $batchTerdekat
                                        ->kode_batch,

                                'tanggal_kadaluarsa' =>
                                    $batchTerdekat
                                        ->tanggal_kadaluarsa
                                        ->format('Y-m-d'),

                                'keterangan_exp' =>
                                    'Exp. ' .
                                    $batchTerdekat
                                        ->tanggal_kadaluarsa
                                        ->format('d/m/Y'),

                                'stok' =>
                                    (int)
                                    $batchTerdekat->stok,

                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | SEMUA BATCH
                    |--------------------------------------------------------------------------
                    */

                    'batches' =>
                        $obat->batches
                            ->values()
                            ->map(function (
                                $batch,
                                $index
                            ) use (
                                $batchTerpakaiIds,
                                $batchTerdekat
                            ) {

                                $sedangDipakai =
                                    in_array(
                                        $batch->id,
                                        $batchTerpakaiIds
                                    );

                                $isTerdekat =
                                    $batchTerdekat
                                    &&
                                    $batch->id ===
                                        $batchTerdekat->id;

                                return [

                                    'id' =>
                                        $batch->id,

                                    'kode_batch' =>
                                        $batch->kode_batch,

                                    'tanggal_masuk' =>
                                        $batch->tanggal_masuk
                                            ? $batch
                                                ->tanggal_masuk
                                                ->format('Y-m-d')
                                            : null,

                                    'tanggal_kadaluarsa' =>
                                        $batch->tanggal_kadaluarsa
                                            ? $batch
                                                ->tanggal_kadaluarsa
                                                ->format('Y-m-d')
                                            : null,

                                    'keterangan_exp' =>
                                        $batch
                                            ->tanggal_kadaluarsa
                                            ? 'Exp. ' .
                                                $batch
                                                    ->tanggal_kadaluarsa
                                                    ->format('d/m/Y')
                                            : null,

                                    'stok' =>
                                        (int)
                                        $batch->stok,

                                    'keterangan' =>
                                        $batch->keterangan,

                                    /*
                                    | Apakah batch paling dekat?
                                    */

                                    'is_terdekat' =>
                                        $isTerdekat,

                                    /*
                                    | Apakah sedang dipakai
                                    | pada kunjungan ini?
                                    */

                                    'sedang_dipakai' =>
                                        $sedangDipakai,
                                ];
                            })
                            ->values(),
                ];
            })
            ->filter(function ($obat) {

                return $obat['batches']
                    ->isNotEmpty();
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TRIASE
        |--------------------------------------------------------------------------
        */

        $triaseList =
            $this->triaseList();

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Edit',
            [

                'kunjungan' =>
                    $kunjungan,

                'siswas' =>
                    $siswas,

                'penyakitList' =>
                    $penyakitList,

                'obatList' =>
                    $obatList,

                'triaseList' =>
                    $triaseList,
            ]
        );
    }


    /**
     * ============================================================
     * UPDATE
     * ============================================================
     */
   /**
 * ============================================================
 * UPDATE KUNJUNGAN
 * ============================================================
 *
 * LOGIKA STOK:
 *
 * 1. Ambil semua detail obat lama.
 * 2. Kembalikan stok lama ke batch masing-masing.
 * 3. Hapus detail obat lama.
 * 4. Update data kunjungan.
 * 5. Validasi dan kurangi stok untuk obat baru.
 *
 * Semua dilakukan dalam DB::transaction().
 *
 * Contoh:
 *
 * Stok awal     = 10
 * Obat lama     = 1
 * Stok sekarang = 9
 *
 * Edit menjadi 6:
 *
 * 9 + 1 = 10
 * 10 - 6 = 4
 *
 * Jadi stok akhir = 4.
 *
 * Jika stok awal = 5:
 *
 * 5 - 6 = tidak cukup
 *
 * Maka transaksi dibatalkan dan stok tetap seperti sebelumnya.
 */
public function update(
    Request $request,
    KunjunganKlinik $kunjungan
) {
    $validated = $request->validate([
        'periode_id' => [
            'required',
            'exists:periodes,id',
        ],

        'siswa_id' => [
            'required',
            'exists:siswas,id',
        ],

        'keluhan' => [
            'nullable',
            'string',
        ],

        'pemeriksaan' => [
            'nullable',
            'string',
        ],

        'penyakit_id' => [
            'nullable',
            'exists:penyakits,id',
        ],

        'triase' => [
            'required',
            'in:merah,kuning,hijau,hitam',
        ],

        'tindakan' => [
            'nullable',
            'string',
        ],

        'catatan' => [
            'nullable',
            'string',
        ],

        'obat' => [
            'nullable',
            'array',
        ],

        'obat.*.id' => [
            'nullable',
            'integer',
        ],

        'obat.*.obat_id' => [
            'required',
            'integer',
            'exists:obats,id',
        ],

        'obat.*.batch_id' => [
            'nullable',
            'integer',
            'exists:obat_batches,id',
        ],

        'obat.*.jumlah' => [
            'required',
            'integer',
            'min:1',
        ],

        'obat.*.keterangan' => [
            'nullable',
            'string',
        ],
    ]);

    DB::transaction(function () use (
        $validated,
        $kunjungan
    ) {

        /*
        |--------------------------------------------------------------------------
        | LOCK KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $kunjungan = KunjunganKlinik::query()
            ->lockForUpdate()
            ->findOrFail($kunjungan->id);


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        $kunjungan->update([
            'periode_id' => $validated['periode_id'],

            'siswa_id' => $validated['siswa_id'],

            'keluhan' =>
                $validated['keluhan'] ?? null,

            'pemeriksaan' =>
                $validated['pemeriksaan'] ?? null,

            'penyakit_id' =>
                $validated['penyakit_id'] ?? null,

            'triase' =>
                $validated['triase'],

            'tindakan' =>
                $validated['tindakan'] ?? null,

            'catatan' =>
                $validated['catatan'] ?? null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | OBAT DARI FORM
        |--------------------------------------------------------------------------
        */

        $obatBaru = $validated['obat'] ?? [];


        /*
        |--------------------------------------------------------------------------
        | CEGAH DUPLIKAT OBAT
        |--------------------------------------------------------------------------
        */

        $obatIds = collect($obatBaru)
            ->pluck('obat_id')
            ->map(fn ($id) => (int) $id);

        if (
            $obatIds->count() !==
            $obatIds->unique()->count()
        ) {
            throw ValidationException::withMessages([
                'obat' =>
                    'Obat yang sama tidak boleh dimasukkan lebih dari satu kali.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL DETAIL OBAT LAMA
        |--------------------------------------------------------------------------
        */

        $obatLama = KunjunganObat::query()
            ->where(
                'kunjungan_id',
                $kunjungan->id
            )
            ->lockForUpdate()
            ->get()
            ->keyBy('id');


        /*
        |--------------------------------------------------------------------------
        | ID DETAIL YANG MASIH ADA DI FORM
        |--------------------------------------------------------------------------
        */

        $detailYangDipakai = collect($obatBaru)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | HAPUS DETAIL YANG SUDAH DIHAPUS DARI FORM
        |--------------------------------------------------------------------------
        */

        foreach ($obatLama as $id => $detailLama) {

            if (
                !$detailYangDipakai->contains(
                    (int) $id
                )
            ) {

                /*
                | Kembalikan stok
                */

                if ($detailLama->batch_id) {

                    $batchLama =
                        ObatBatch::query()
                            ->lockForUpdate()
                            ->find(
                                $detailLama->batch_id
                            );

                    if ($batchLama) {

                        $batchLama->increment(
                            'stok',
                            (int) $detailLama->jumlah
                        );
                    }
                }

                /*
                | Hapus detail
                */

                $detailLama->delete();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PROSES OBAT
        |--------------------------------------------------------------------------
        */

        foreach (
            $obatBaru as $index => $item
        ) {

            $detailId =
                !empty($item['id'])
                    ? (int) $item['id']
                    : null;


            /*
            |--------------------------------------------------------------------------
            | CARI DETAIL LAMA
            |--------------------------------------------------------------------------
            */

            $detailLama = null;

            if ($detailId) {

                $detailLama =
                    $obatLama->get($detailId);

                if (
                    !$detailLama ||
                    (int) $detailLama->kunjungan_id !==
                        (int) $kunjungan->id
                ) {

                    throw ValidationException::withMessages([
                        "obat.$index.id" =>
                            'Detail obat tidak valid.',
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | DATA LAMA
            |--------------------------------------------------------------------------
            */

            $obatIdLama =
                $detailLama
                    ? (int) $detailLama->obat_id
                    : null;

            $batchIdLama =
                $detailLama
                    ? (int) $detailLama->batch_id
                    : null;

            $jumlahLama =
                $detailLama
                    ? (int) $detailLama->jumlah
                    : 0;


            /*
            |--------------------------------------------------------------------------
            | DATA BARU
            |--------------------------------------------------------------------------
            */

            $obatIdBaru =
                (int) $item['obat_id'];

            $jumlahBaru =
                (int) $item['jumlah'];

            $batchIdBaru =
                !empty($item['batch_id'])
                    ? (int) $item['batch_id']
                    : null;


            /*
            |--------------------------------------------------------------------------
            | OBAT
            |--------------------------------------------------------------------------
            */

            $obat = Obat::query()
                ->find($obatIdBaru);

            if (!$obat) {

                throw ValidationException::withMessages([
                    "obat.$index.obat_id" =>
                        'Obat tidak ditemukan.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | JIKA BATCH TIDAK DITENTUKAN
            |--------------------------------------------------------------------------
            */

            if (!$batchIdBaru) {

                $batchBaru =
                    ObatBatch::query()
                        ->where(
                            'obat_id',
                            $obatIdBaru
                        )
                        ->where(
                            'stok',
                            '>',
                            0
                        )
                        ->whereDate(
                            'tanggal_kadaluarsa',
                            '>',
                            today()->addDay()
                        )
                        ->orderBy(
                            'tanggal_kadaluarsa'
                        )
                        ->orderBy(
                            'tanggal_masuk'
                        )
                        ->lockForUpdate()
                        ->first();

                if (!$batchBaru) {

                    throw ValidationException::withMessages([
                        "obat.$index.batch_id" =>
                            "Obat {$obat->nama_obat} tidak memiliki batch aktif.",
                    ]);
                }

                $batchIdBaru =
                    (int) $batchBaru->id;

            } else {

                $batchBaru =
                    ObatBatch::query()
                        ->lockForUpdate()
                        ->find(
                            $batchIdBaru
                        );

                if (!$batchBaru) {

                    throw ValidationException::withMessages([
                        "obat.$index.batch_id" =>
                            'Batch obat tidak ditemukan.',
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | BATCH HARUS SESUAI OBAT
            |--------------------------------------------------------------------------
            */

            if (
                (int) $batchBaru->obat_id !==
                $obatIdBaru
            ) {

                throw ValidationException::withMessages([
                    "obat.$index.batch_id" =>
                        'Batch tidak sesuai dengan obat.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | EDIT OBAT LAMA
            |--------------------------------------------------------------------------
            */

            if ($detailLama) {

                /*
                |--------------------------------------------------------------
                | BATCH SAMA
                |--------------------------------------------------------------
                */

                if (
                    $batchIdLama ===
                    $batchIdBaru
                ) {

                    $selisih =
                        $jumlahBaru -
                        $jumlahLama;


                    /*
                    | Jumlah bertambah
                    */

                    if ($selisih > 0) {

                        if (
                            (int) $batchBaru->stok <
                            $selisih
                        ) {

                            throw ValidationException::withMessages([
                                "obat.$index.jumlah" =>
                                    "Stok tidak mencukupi. " .
                                    "Stok tersedia: {$batchBaru->stok}.",
                            ]);
                        }

                        $batchBaru->decrement(
                            'stok',
                            $selisih
                        );
                    }


                    /*
                    | Jumlah berkurang
                    */

                    elseif ($selisih < 0) {

                        $batchBaru->increment(
                            'stok',
                            abs($selisih)
                        );
                    }

                }

                /*
                |--------------------------------------------------------------
                | BATCH BERUBAH
                |--------------------------------------------------------------
                */

                else {

                    /*
                    | Kembalikan stok batch lama
                    */

                    if ($batchIdLama) {

                        $batchLama =
                            ObatBatch::query()
                                ->lockForUpdate()
                                ->find(
                                    $batchIdLama
                                );

                        if ($batchLama) {

                            $batchLama->increment(
                                'stok',
                                $jumlahLama
                            );
                        }
                    }


                    /*
                    | Kurangi stok batch baru
                    */

                    if (
                        (int) $batchBaru->stok <
                        $jumlahBaru
                    ) {

                        throw ValidationException::withMessages([
                            "obat.$index.jumlah" =>
                                "Stok batch baru tidak mencukupi.",
                        ]);
                    }

                    $batchBaru->decrement(
                        'stok',
                        $jumlahBaru
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE DETAIL OBAT
                |--------------------------------------------------------------------------
                */

                $detailLama->update([
                    'obat_id' =>
                        $obatIdBaru,

                    'batch_id' =>
                        $batchIdBaru,

                    'jumlah' =>
                        $jumlahBaru,

                    'keterangan' =>
                        $item['keterangan']
                        ?? null,
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | OBAT BARU
            |--------------------------------------------------------------------------
            */

            else {

                if (
                    (int) $batchBaru->stok <
                    $jumlahBaru
                ) {

                    throw ValidationException::withMessages([
                        "obat.$index.jumlah" =>
                            "Stok batch tidak mencukupi. " .
                            "Stok tersedia: {$batchBaru->stok}.",
                    ]);
                }


                /*
                | Kurangi stok
                */

                $batchBaru->decrement(
                    'stok',
                    $jumlahBaru
                );


                /*
                | Buat detail baru
                */

                KunjunganObat::create([
                    'kunjungan_id' =>
                        $kunjungan->id,

                    'obat_id' =>
                        $obatIdBaru,

                    'batch_id' =>
                        $batchIdBaru,

                    'jumlah' =>
                        $jumlahBaru,

                    'keterangan' =>
                        $item['keterangan']
                        ?? null,
                ]);
            }
        }
    });


    $kunjungan->load('siswa');

    NotificationService::toRole(
        'admin',
        'Kunjungan Klinik Diperbarui',
        "Data kunjungan {$kunjungan->siswa->nama} telah diperbarui oleh Klinik.",
        'info',
        route('admin.kunjungan.show', $kunjungan)
    );

    return redirect()
        ->route(
            'klinik.kesehatan.kunjungan.index'
        )
        ->with(
            'success',
            'Data kunjungan berhasil diperbarui.'
        );
}

    /**
     * ============================================================
     * DELETE
     * ============================================================
     */
    public function destroy(
        KunjunganKlinik $kunjungan
    ) {

        $kunjungan->load('siswa');
        $deletedStudentName = $kunjungan->siswa?->nama ?? 'Siswa';

        DB::transaction(function () use (
            $kunjungan
        ) {

            $kunjungan->load(
                'kunjunganObat'
            );

            foreach (
                $kunjungan->kunjunganObat
                as $item
            ) {

                $batch = ObatBatch::lockForUpdate()
                    ->find(
                        $item->batch_id
                    );

                if ($batch) {

                    $batch->increment(
                        'stok',
                        $item->jumlah
                    );
                }
            }

            $kunjungan
                ->kunjunganObat()
                ->delete();

            $kunjungan->delete();
        });

        NotificationService::toRole(
            'admin',
            'Kunjungan Klinik Dihapus',
            "Kunjungan klinik {$deletedStudentName} telah dihapus oleh Klinik.",
            'warning',
            route('admin.kunjungan.index')
        );

        return redirect()
            ->route(
                'klinik.kesehatan.kunjungan.index'
            )
            ->with(
                'success',
                'Data kunjungan berhasil dihapus dan stok batch obat dikembalikan.'
            );
    }


    /**
     * ============================================================
     * HELPER:
     * KURANGI STOK OBAT
     * ============================================================
     *
     * ATURAN:
     *
     * 1. Batch harus punya stok.
     * 2. Batch harus belum H-1 expired.
     * 3. Jika batch_id dikirim, gunakan batch tersebut.
     * 4. Jika batch_id kosong, pilih otomatis batch
     *    dengan expiry terdekat.
     *
     */
   /**
 * ============================================================
 * HELPER:
 * KURANGI STOK OBAT
 * ============================================================
 *
 * ATURAN:
 *
 * 1. obat_id wajib sesuai dengan batch_id.
 * 2. Batch harus tersedia.
 * 3. Batch tidak boleh expired.
 * 4. Batch H-1 tidak boleh digunakan.
 * 5. Stok tidak boleh negatif.
 * 6. Jika batch_id dikirim, gunakan batch tersebut.
 * 7. Jika batch_id kosong, gunakan FIFO berdasarkan expiry.
 *
 */
private function kurangiStokObat(
    KunjunganKlinik $kunjungan,
    array $item
): void {

    $obatId =
        (int) $item['obat_id'];

    $jumlahDiminta =
        (int) $item['jumlah'];

    /*
    |--------------------------------------------------------------------------
    | VALIDASI JUMLAH
    |--------------------------------------------------------------------------
    */

    if ($jumlahDiminta <= 0) {

        throw ValidationException::withMessages([

            'obat' =>
                'Jumlah obat harus lebih dari 0.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BATAS KADALUARSA
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Hari ini 25
    |
    | Exp 25 -> tidak boleh
    | Exp 26 -> tidak boleh
    | Exp 27 -> boleh
    |
    */

    $batasKadaluarsa =
        today()->addDay();

    /*
    |--------------------------------------------------------------------------
    | JIKA BATCH DIPILIH MANUAL
    |--------------------------------------------------------------------------
    */

    if (
        !empty($item['batch_id'])
    ) {

        $batch = ObatBatch::query()
            ->where(
                'id',
                $item['batch_id']
            )
            ->where(
                'obat_id',
                $obatId
            )
            ->lockForUpdate()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | BATCH TIDAK DITEMUKAN
        |--------------------------------------------------------------------------
        */

        if (!$batch) {

            throw ValidationException::withMessages([

                'obat' =>
                    'Batch obat yang dipilih tidak ditemukan atau tidak sesuai dengan obat.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK TANGGAL EXPIRED
        |--------------------------------------------------------------------------
        */

        if (
            !$batch->tanggal_kadaluarsa
            ||
            $batch
                ->tanggal_kadaluarsa
                ->lte(
                    $batasKadaluarsa
                )
        ) {

            $tanggalExp =
                $batch->tanggal_kadaluarsa
                    ? $batch
                        ->tanggal_kadaluarsa
                        ->format('d/m/Y')
                    : '-';

            throw ValidationException::withMessages([

                'obat' =>
                    "Batch {$batch->kode_batch} tidak dapat digunakan karena sudah H-1 atau kadaluarsa. Exp: {$tanggalExp}.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK STOK
        |--------------------------------------------------------------------------
        */

        $stokTersedia =
            (int) $batch->stok;

        if (
            $stokTersedia <
            $jumlahDiminta
        ) {

            throw ValidationException::withMessages([

                'obat' =>
                    "Stok batch {$batch->kode_batch} tidak mencukupi. " .
                    "Stok tersedia: {$stokTersedia}, " .
                    "jumlah diminta: {$jumlahDiminta}.",
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KURANGI STOK
        |--------------------------------------------------------------------------
        */

        $batch->stok =
            $stokTersedia -
            $jumlahDiminta;

        $batch->save();

        if ($batch->stok <= 5) {
            $batch->loadMissing('obat');
            NotificationService::toRole(
                'klinik',
                'Stok Obat Menipis',
                "Stok {$batch->obat->nama_obat} tersisa {$batch->stok} unit.",
                'warning',
                route('klinik.obat.index')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DETAIL KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        KunjunganObat::create([

            'kunjungan_id' =>
                $kunjungan->id,

            'obat_id' =>
                $obatId,

            'batch_id' =>
                $batch->id,

            'jumlah' =>
                $jumlahDiminta,

            'keterangan' =>
                $item['keterangan'] ?? null,
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | JIKA BATCH TIDAK DIPILIH
    |--------------------------------------------------------------------------
    |
    | Gunakan FIFO berdasarkan:
    |
    | 1. Expiry terdekat
    | 2. Tanggal masuk
    | 3. ID batch
    |
    */

    $batches = ObatBatch::query()
        ->where(
            'obat_id',
            $obatId
        )

        ->where(
            'stok',
            '>',
            0
        )

        ->whereNotNull(
            'tanggal_kadaluarsa'
        )

        ->whereDate(
            'tanggal_kadaluarsa',
            '>',
            $batasKadaluarsa
        )

        ->orderBy(
            'tanggal_kadaluarsa',
            'asc'
        )

        ->orderBy(
            'tanggal_masuk',
            'asc'
        )

        ->orderBy(
            'id',
            'asc'
        )

        ->lockForUpdate()
        ->get();

    /*
    |--------------------------------------------------------------------------
    | HITUNG TOTAL STOK
    |--------------------------------------------------------------------------
    */

    $totalStok =
        (int) $batches->sum('stok');

    /*
    |--------------------------------------------------------------------------
    | CEK TOTAL STOK
    |--------------------------------------------------------------------------
    */

    if (
        $totalStok <
        $jumlahDiminta
    ) {

        throw ValidationException::withMessages([

            'obat' =>
                "Stok {$this->namaObat($obatId)} tidak mencukupi. " .
                "Stok tersedia: {$totalStok}, " .
                "jumlah diminta: {$jumlahDiminta}.",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JUMLAH YANG MASIH HARUS DIAMBIL
    |--------------------------------------------------------------------------
    */

    $sisa =
        $jumlahDiminta;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DARI BATCH FIFO
    |--------------------------------------------------------------------------
    */

    foreach (
        $batches as $batch
    ) {

        if (
            $sisa <= 0
        ) {
            break;
        }

        $stokBatch =
            (int) $batch->stok;

        $jumlahDiambil =
            min(
                $stokBatch,
                $sisa
            );

        /*
        |--------------------------------------------------------------------------
        | KURANGI STOK BATCH
        |--------------------------------------------------------------------------
        */

        $batch->stok =
            $stokBatch -
            $jumlahDiambil;

        $batch->save();

        if ($batch->stok <= 5) {
            $batch->loadMissing('obat');
            NotificationService::toRole(
                'klinik',
                'Stok Obat Menipis',
                "Stok {$batch->obat->nama_obat} tersisa {$batch->stok} unit.",
                'warning',
                route('klinik.obat.index')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN RIWAYAT
        |--------------------------------------------------------------------------
        */

        KunjunganObat::create([

            'kunjungan_id' =>
                $kunjungan->id,

            'obat_id' =>
                $obatId,

            'batch_id' =>
                $batch->id,

            'jumlah' =>
                $jumlahDiambil,

            'keterangan' =>
                $item['keterangan'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | KURANGI SISA
        |--------------------------------------------------------------------------
        */

        $sisa -=
            $jumlahDiambil;
    }

    /*
    |--------------------------------------------------------------------------
    | PENGAMAN
    |--------------------------------------------------------------------------
    |
    | Secara normal tidak akan sampai sini jika total stok
    | sudah divalidasi.
    |
    */

    if (
        $sisa > 0
    ) {

        throw ValidationException::withMessages([

            'obat' =>
                "Stok {$this->namaObat($obatId)} tidak mencukupi untuk memenuhi jumlah yang diminta.",
        ]);
    }
}

    /**
     * ============================================================
     * HELPER NAMA OBAT
     * ============================================================
     */
    private function namaObat(
        int $obatId
    ): string {

        return Obat::where(
            'id',
            $obatId
        )->value('nama_obat')
            ?? 'Obat';
    }


    /**
     * ============================================================
     * HELPER TRIASE
     * ============================================================
     */
    private function triaseList(): array
    {
        return [

            [
                'value' =>
                    'merah',

                'label' =>
                    'Merah',

                'prioritas' =>
                    'Prioritas Tinggi',

                'deskripsi' =>
                    'Gawat darurat dan mengancam nyawa.',
            ],

            [
                'value' =>
                    'kuning',

                'label' =>
                    'Kuning',

                'prioritas' =>
                    'Prioritas Sedang',

                'deskripsi' =>
                    'Darurat tetapi tidak ada ancaman kematian segera.',
            ],

            [
                'value' =>
                    'hijau',

                'label' =>
                    'Hijau',

                'prioritas' =>
                    'Prioritas Rendah',

                'deskripsi' =>
                    'Tidak gawat dan tidak ada ancaman kematian.',
            ],

            [
                'value' =>
                    'hitam',

                'label' =>
                    'Hitam',

                'prioritas' =>
                    'Prioritas Rendah',

                'deskripsi' =>
                    'Darurat tidak gawat dan tidak ada harapan hidup.',
            ],
        ];
    }
}