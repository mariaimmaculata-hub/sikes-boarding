<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\KunjunganKlinik;
use App\Models\KunjunganObat;
use App\Models\Obat;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

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
        |
        | Tanggal kunjungan mengikuti created_at.
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

                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL INPUT / KUNJUNGAN
                    |
                    | Menggunakan created_at karena tanggal otomatis
                    | dibuat oleh sistem saat data disimpan.
                    |--------------------------------------------------------------------------
                    */
                    'created_at' => $item->created_at
                        ? $item->created_at->format('d/m/Y H:i')
                        : null,

                    'tanggal' => $item->created_at
                        ? $item->created_at->format('Y-m-d')
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATED AT
                    |--------------------------------------------------------------------------
                    */
                    'updated_at' => $item->updated_at
                        ? $item->updated_at->format('d/m/Y H:i')
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | DATA KESEHATAN
                    |--------------------------------------------------------------------------
                    */
                    'keluhan' => $item->keluhan,

                    'pemeriksaan' => $item->pemeriksaan,

                    /*
                    |--------------------------------------------------------------------------
                    | PENYAKIT / DIAGNOSIS
                    |--------------------------------------------------------------------------
                    */
                    'penyakit' => $item->penyakit
                        ? [
                            'id' => $item->penyakit->id,
                            'nama_penyakit' => $item->penyakit->nama_penyakit,
                            'kategori' => $item->penyakit->kategori,
                            'keterangan' => $item->penyakit->keterangan,
                        ]
                        : null,

                    'diagnosis' => $item->penyakit?->nama_penyakit,

                    /*
                    |--------------------------------------------------------------------------
                    | TINDAKAN
                    |--------------------------------------------------------------------------
                    */
                    'tindakan' => $item->tindakan,

                    /*
                    |--------------------------------------------------------------------------
                    | CATATAN
                    |--------------------------------------------------------------------------
                    */
                    'catatan' => $item->catatan,

                    /*
                    |--------------------------------------------------------------------------
                    | SISWA
                    |--------------------------------------------------------------------------
                    */
                    'siswa' => $item->siswa
                        ? [
                            'id' => $item->siswa->id,

                            'nisn' => $item->siswa->nisn,

                            'nama' => $item->siswa->nama,

                            'kelas' => $item->siswa->kelas
                                ? [
                                    'id' => $item->siswa->kelas->id,
                                    'nama_kelas' => $item->siswa->kelas->nama_kelas,
                                ]
                                : null,

                            'jurusan' => $item->siswa->kelas?->jurusan
                                ? [
                                    'id' => $item->siswa->kelas->jurusan->id,
                                    'nama_jurusan' => $item->siswa->kelas->jurusan->nama_jurusan,
                                ]
                                : null,
                        ]
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | PEMERIKSA
                    |--------------------------------------------------------------------------
                    */
                    'pemeriksa' => $item->pemeriksa
                        ? [
                            'id' => $item->pemeriksa->id,
                            'name' => $item->pemeriksa->name,
                        ]
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | PERIODE
                    |--------------------------------------------------------------------------
                    */
                    'periode' => $item->periode
                        ? [
                            'id' => $item->periode->id,
                            'nama_periode' => $item->periode->nama_periode,
                        ]
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | OBAT
                    |--------------------------------------------------------------------------
                    */
                    'obat' => $item->kunjunganObat
                        ->map(function ($itemObat) {
                            return [
                                'id' => $itemObat->id,

                                'obat_id' => $itemObat->obat_id,

                                'nama_obat' => $itemObat->obat?->nama_obat,

                                'satuan' => $itemObat->obat?->satuan,

                                'jumlah' => $itemObat->jumlah,

                                'keterangan' => $itemObat->keterangan,
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
                    'id' => $periode->id,

                    'nama_periode' => $periode->nama_periode,

                    'tanggal_mulai' => $periode->tanggal_mulai,

                    'tanggal_selesai' => $periode->tanggal_selesai,
                ];
            });
/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$totalKunjungan = KunjunganKlinik::count();

$kunjunganHariIni = KunjunganKlinik::whereDate(
    'created_at',
    today()
)->count();


/*
|--------------------------------------------------------------------------
| TREN PENYAKIT
|--------------------------------------------------------------------------
|
| Menghitung jumlah kunjungan berdasarkan penyakit.
|
*/

$trendPenyakitQuery = KunjunganKlinik::query()
    ->select(
        'penyakit_id',
        DB::raw('COUNT(*) as jumlah')
    )
    ->whereNotNull('penyakit_id')
    ->groupBy('penyakit_id')
    ->with('penyakit');


// Jika sedang memilih periode,
// statistik penyakit mengikuti periode tersebut.
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
            'id' => $item->penyakit_id,

            'nama_penyakit' =>
                $item->penyakit?->nama_penyakit
                ?? 'Tidak diketahui',

            'jumlah' => (int) $item->jumlah,
        ];
    })
    ->sortByDesc('jumlah')
    ->values();


// Jumlah maksimal untuk menentukan panjang bar
$maxTrendPenyakit = $trendPenyakit->max('jumlah') ?? 0;
        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */
        return Inertia::render(
    'Klinik/Kesehatan/Kunjungan/Index',
    [
        'kunjungan' => $kunjungan,

        'periodeList' => $periodeList,

        'statistik' => [
            'total' => $totalKunjungan,

            'hari_ini' => $kunjunganHariIni,

            'trend_penyakit' => $trendPenyakit,

            'max_trend_penyakit' => $maxTrendPenyakit,
        ],

        'filter' => [
            'search' => $request->search,

            'periode_id' => $request->periode_id,

            'tanggal' => $request->tanggal,
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
        | DATA SISWA
        |--------------------------------------------------------------------------
        */
        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) {
                return [
                    'id' => $siswa->id,

                    'nisn' => $siswa->nisn,

                    'nama' => $siswa->nama,

                    'kelas' => $siswa->kelas
                        ? [
                            'id' => $siswa->kelas->id,

                            'nama_kelas' => $siswa->kelas->nama_kelas,
                        ]
                        : null,

                    'jurusan' => $siswa->kelas?->jurusan
                        ? [
                            'id' => $siswa->kelas->jurusan->id,

                            'nama_jurusan' => $siswa->kelas->jurusan->nama_jurusan,
                        ]
                        : null,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | DATA PENYAKIT
        |--------------------------------------------------------------------------
        */
        $penyakitList = Penyakit::query()
            ->orderBy('nama_penyakit')
            ->get()
            ->map(function ($penyakit) {
                return [
                    'id' => $penyakit->id,

                    'nama_penyakit' => $penyakit->nama_penyakit,

                    'kategori' => $penyakit->kategori,

                    'keterangan' => $penyakit->keterangan,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | DATA OBAT
        |--------------------------------------------------------------------------
        */
        $obatList = Obat::query()
            ->where('stok', '>', 0)
            ->orderBy('nama_obat')
            ->get()
            ->map(function ($obat) {
                return [
                    'id' => $obat->id,

                    'nama_obat' => $obat->nama_obat,

                    'satuan' => $obat->satuan,

                    'stok' => $obat->stok,

                    'keterangan' => $obat->keterangan,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RETURN FORM
        |--------------------------------------------------------------------------
        */
        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Create',
            [
                'periode' => $periode
                    ? [
                        'id' => $periode->id,

                        'nama_periode' => $periode->nama_periode,
                    ]
                    : null,

                'siswas' => $siswas,

                'penyakitList' => $penyakitList,

                'obatList' => $obatList,
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
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |
        | Tidak ada:
        | - tanggal_kunjungan
        | - status
        |
        | Keduanya ditentukan sistem.
        |--------------------------------------------------------------------------
        */
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

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | SIMPAN KUNJUNGAN
            |
            | created_at otomatis diisi Laravel.
            | tanggal_kunjungan juga diisi menggunakan waktu sistem.
            |
            | Tidak ada input tanggal dari user.
            |--------------------------------------------------------------------------
            */
            $kunjungan = KunjunganKlinik::create([
                'periode_id' => $validated['periode_id'],

                'tanggal_kunjungan' => now(),

                'siswa_id' => $validated['siswa_id'],

                'keluhan' => $validated['keluhan'] ?? null,

                'pemeriksaan' => $validated['pemeriksaan'] ?? null,

                'penyakit_id' => $validated['penyakit_id'] ?? null,

                'tindakan' => $validated['tindakan'] ?? null,

                'catatan' => $validated['catatan'] ?? null,

                'pemeriksa_id' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | SIMPAN OBAT DAN KURANGI STOK
            |--------------------------------------------------------------------------
            */
            if (!empty($validated['obat'])) {

                foreach ($validated['obat'] as $item) {

                    $obat = Obat::lockForUpdate()
                        ->findOrFail(
                            $item['obat_id']
                        );

                    if (
                        $obat->stok <
                        $item['jumlah']
                    ) {
                        throw new \Exception(
                            "Stok obat {$obat->nama_obat} tidak mencukupi. Stok tersedia: {$obat->stok}."
                        );
                    }

                    KunjunganObat::create([
                        'kunjungan_id' => $kunjungan->id,

                        'obat_id' => $obat->id,

                        'jumlah' => $item['jumlah'],

                        'keterangan' => $item['keterangan'] ?? null,
                    ]);

                    $obat->decrement(
                        'stok',
                        $item['jumlah']
                    );
                }
            }
        });

        return redirect()
            ->route(
                'klinik.kesehatan.kunjungan.index'
            )
            ->with(
                'success',
                'Data kunjungan klinik berhasil disimpan.'
            );
    }


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
        ]);

        return Inertia::render(
            'Klinik/Kesehatan/Kunjungan/Show',
            [
                'kunjungan' => [
                    'id' => $kunjungan->id,

                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL INPUT
                    |
                    | Tanggal ditampilkan dari created_at.
                    |--------------------------------------------------------------------------
                    */
                    'created_at' => $kunjungan->created_at
                        ? $kunjungan->created_at->format('d/m/Y H:i')
                        : null,

                    /*
                    |--------------------------------------------------------------------------
                    | TANGGAL KUNJUNGAN
                    |
                    | Tetap dikirim jika kolom ini masih digunakan
                    | oleh view/PDF.
                    |--------------------------------------------------------------------------
                    */
                    'tanggal_kunjungan' => $kunjungan->tanggal_kunjungan
                        ? \Carbon\Carbon::parse(
                            $kunjungan->tanggal_kunjungan
                        )->format('d/m/Y H:i')
                        : (
                            $kunjungan->created_at
                                ? $kunjungan->created_at->format('d/m/Y H:i')
                                : null
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATED AT
                    |--------------------------------------------------------------------------
                    */
                    'updated_at' => $kunjungan->updated_at
                        ? $kunjungan->updated_at->format('d/m/Y H:i')
                        : null,

                    'keluhan' => $kunjungan->keluhan,

                    'pemeriksaan' => $kunjungan->pemeriksaan,

                    /*
                    |--------------------------------------------------------------------------
                    | PENYAKIT
                    |--------------------------------------------------------------------------
                    */
                    'penyakit' => $kunjungan->penyakit
                        ? [
                            'id' => $kunjungan->penyakit->id,

                            'nama_penyakit' =>
                                $kunjungan->penyakit->nama_penyakit,

                            'kategori' =>
                                $kunjungan->penyakit->kategori,

                            'keterangan' =>
                                $kunjungan->penyakit->keterangan,
                        ]
                        : null,

                    'diagnosis' =>
                        $kunjungan->penyakit?->nama_penyakit,

                    /*
                    |--------------------------------------------------------------------------
                    | TINDAKAN
                    |--------------------------------------------------------------------------
                    */
                    'tindakan' =>
                        $kunjungan->tindakan,

                    /*
                    |--------------------------------------------------------------------------
                    | CATATAN
                    |--------------------------------------------------------------------------
                    */
                    'catatan' =>
                        $kunjungan->catatan,

                    /*
                    |--------------------------------------------------------------------------
                    | SISWA
                    |--------------------------------------------------------------------------
                    */
                    'siswa' =>
                        $kunjungan->siswa
                            ? [
                                'id' =>
                                    $kunjungan->siswa->id,

                                'nisn' =>
                                    $kunjungan->siswa->nisn,

                                'nama' =>
                                    $kunjungan->siswa->nama,

                                'kelas' =>
                                    $kunjungan->siswa->kelas
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

                    /*
                    |--------------------------------------------------------------------------
                    | PEMERIKSA
                    |--------------------------------------------------------------------------
                    */
                    'pemeriksa' =>
                        $kunjungan->pemeriksa
                            ? [
                                'id' =>
                                    $kunjungan->pemeriksa->id,

                                'name' =>
                                    $kunjungan->pemeriksa->name,
                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | PERIODE
                    |--------------------------------------------------------------------------
                    */
                    'periode' =>
                        $kunjungan->periode
                            ? [
                                'id' =>
                                    $kunjungan->periode->id,

                                'nama_periode' =>
                                    $kunjungan->periode
                                        ->nama_periode,
                            ]
                            : null,

                    /*
                    |--------------------------------------------------------------------------
                    | OBAT
                    |--------------------------------------------------------------------------
                    */
                    'obat' =>
                        $kunjungan->kunjunganObat
                            ->map(function ($item) {
                                return [
                                    'id' =>
                                        $item->id,

                                    'obat_id' =>
                                        $item->obat_id,

                                    'nama_obat' =>
                                        $item->obat?->nama_obat,

                                    'satuan' =>
                                        $item->obat?->satuan,

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
     * PRINT DETAIL KUNJUNGAN
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
        ]);

        return view(
            'pdf.kunjungan-klinik',
            [
                'kunjungan' => $kunjungan,
            ]
        );
    }


    /**
     * ============================================================
     * DOWNLOAD PDF
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
        ]);

        $pdf = Pdf::loadView(
            'pdf.kunjungan-klinik',
            [
                'kunjungan' => $kunjungan,
            ]
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        $namaSiswa = $kunjungan->siswa?->nama
            ? preg_replace(
                '/[^A-Za-z0-9\-]/',
                '-',
                $kunjungan->siswa->nama
            )
            : 'siswa';

        /*
        |--------------------------------------------------------------------------
        | NAMA FILE MENGGUNAKAN CREATED_AT
        |--------------------------------------------------------------------------
        */
        $tanggal = $kunjungan->created_at
            ? $kunjungan->created_at->format('Y-m-d')
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
            'kunjunganObat.obat',
            'periode',
        ]);

        /*
        |--------------------------------------------------------------------------
        | DATA SISWA
        |--------------------------------------------------------------------------
        */
        $siswas = Siswa::with([
            'kelas.jurusan',
        ])
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) {
                return [
                    'id' => $siswa->id,

                    'nisn' => $siswa->nisn,

                    'nama' => $siswa->nama,

                    'kelas' => $siswa->kelas
                        ? [
                            'id' =>
                                $siswa->kelas->id,

                            'nama_kelas' =>
                                $siswa->kelas->nama_kelas,
                        ]
                        : null,

                    'jurusan' => $siswa->kelas?->jurusan
                        ? [
                            'id' =>
                                $siswa->kelas->jurusan->id,

                            'nama_jurusan' =>
                                $siswa
                                    ->kelas
                                    ->jurusan
                                    ->nama_jurusan,
                        ]
                        : null,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | DATA PENYAKIT
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
        | DATA OBAT
        |--------------------------------------------------------------------------
        */
        $obatList = Obat::query()
            ->orderBy('nama_obat')
            ->get()
            ->map(function ($obat) {
                return [
                    'id' =>
                        $obat->id,

                    'nama_obat' =>
                        $obat->nama_obat,

                    'satuan' =>
                        $obat->satuan,

                    'stok' =>
                        $obat->stok,

                    'keterangan' =>
                        $obat->keterangan,
                ];
            })
            ->values();

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
            ]
        );
    }


    /**
     * ============================================================
     * UPDATE
     * ============================================================
     */
    public function update(
        Request $request,
        KunjunganKlinik $kunjungan
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |
        | Tidak ada:
        | - tanggal_kunjungan
        | - status
        |--------------------------------------------------------------------------
        */
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

            'tindakan' => [
                'nullable',
                'string',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |
        | created_at TIDAK BERUBAH.
        | tanggal_kunjungan TIDAK BERUBAH.
        | updated_at otomatis berubah.
        |--------------------------------------------------------------------------
        */
        $kunjungan->update([
            'periode_id' =>
                $validated['periode_id'],

            'siswa_id' =>
                $validated['siswa_id'],

            'keluhan' =>
                $validated['keluhan'] ?? null,

            'pemeriksaan' =>
                $validated['pemeriksaan'] ?? null,

            'penyakit_id' =>
                $validated['penyakit_id'] ?? null,

            'tindakan' =>
                $validated['tindakan'] ?? null,

            'catatan' =>
                $validated['catatan'] ?? null,
        ]);

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
        DB::transaction(function () use ($kunjungan) {

            $kunjungan->load(
                'kunjunganObat'
            );

            /*
            |--------------------------------------------------------------------------
            | KEMBALIKAN STOK OBAT
            |--------------------------------------------------------------------------
            */
            foreach (
                $kunjungan->kunjunganObat
                as $item
            ) {

                $obat = Obat::lockForUpdate()
                    ->find(
                        $item->obat_id
                    );

                if ($obat) {
                    $obat->increment(
                        'stok',
                        $item->jumlah
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS DETAIL OBAT
            |--------------------------------------------------------------------------
            */
            $kunjungan
                ->kunjunganObat()
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | HAPUS KUNJUNGAN
            |--------------------------------------------------------------------------
            */
            $kunjungan->delete();
        });

        return redirect()
            ->route(
                'klinik.kesehatan.kunjungan.index'
            )
            ->with(
                'success',
                'Data kunjungan berhasil dihapus dan stok obat dikembalikan.'
            );
    }
}