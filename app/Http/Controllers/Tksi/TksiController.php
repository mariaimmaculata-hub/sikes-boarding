<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Siswa;
use App\Models\TksiHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TksiController extends Controller
{
    /**
     * Daftar siswa TKSI pada periode aktif
     */
    public function index()
{
    $periode = Periode::where('status', 'aktif')->first();

    if (!$periode) {
        return Inertia::render('Tksi/Input/Index', [
            'periode' => null,
            'siswa' => [],
            'statistik' => [
                'total' => 0,
                'lengkap' => 0,
                'belum_lengkap' => 0,
            ],
            'success' => session('success'),
        ]);
    }

    $siswa = $periode->siswa()
        ->with('kelas.jurusan')
        ->where('status', 'aktif')
        ->orderBy('nama')
        ->get();

    $jumlahKomponen = count($this->komponen());

    $hasil = TksiHasil::where('periode_id', $periode->id)
        ->get()
        ->groupBy('siswa_id');

    $siswa = $siswa->map(function ($student) use ($hasil, $jumlahKomponen) {

        $hasilSiswa = $hasil->get($student->id, collect());

        $jumlahHasil = $hasilSiswa->count();

        return [
            'id' => $student->id,
            'nisn' => $student->nisn,
            'nama' => $student->nama,
            'jenis_kelamin' => $student->jenis_kelamin,

            'kelas' => $student->kelas
                ? [
                    'id' => $student->kelas->id,
                    'nama_kelas' => $student->kelas->nama_kelas,
                ]
                : null,

            'jumlah_hasil' => $jumlahHasil,

            'lengkap' => $jumlahHasil >= $jumlahKomponen,

            'hasil' => $hasilSiswa->mapWithKeys(function ($item) {
                return [
                    $item->komponen => [
                        'nilai' => $item->nilai,
                        'catatan' => $item->catatan,
                    ],
                ];
            }),
        ];
    });

    return Inertia::render('Tksi/Input/Index', [
        'periode' => $periode,

        'siswa' => $siswa,

        'statistik' => [
            'total' => $siswa->count(),

            'lengkap' => $siswa
                ->where('lengkap', true)
                ->count(),

            'belum_lengkap' => $siswa
                ->where('lengkap', false)
                ->count(),
        ],

        'success' => session('success'),
    ]);
}
    /**
     * Form input TKSI
     */
  public function create(Siswa $siswa)
{
    $periode = Periode::where('status', 'aktif')->first();

    if (!$periode) {
        return Inertia::render('Tksi/Input/Create', [
            'periode' => null,
            'siswaTerpilih' => null,
            'komponen' => $this->komponen(),
            'hasil' => [],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pastikan siswa terdaftar pada periode aktif
    |--------------------------------------------------------------------------
    */

    $terdaftar = $periode->siswa()
        ->where('siswas.id', $siswa->id)
        ->where('status', 'aktif')
        ->exists();

    if (!$terdaftar) {
        return redirect()
            ->route('tksi.input.index')
            ->withErrors([
                'siswa' => 'Siswa tidak terdaftar pada periode aktif.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil data siswa
    |--------------------------------------------------------------------------
    */

    $siswa->load('kelas.jurusan');

    /*
    |--------------------------------------------------------------------------
    | Ambil hasil TKSI siswa
    |--------------------------------------------------------------------------
    */

    $hasil = TksiHasil::where('periode_id', $periode->id)
        ->where('siswa_id', $siswa->id)
        ->get([
            'id',
            'tanggal',
            'komponen',
            'nilai',
            'catatan',
        ])
        ->mapWithKeys(function ($item) {
            return [
                $item->komponen => [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal?->format('Y-m-d'),
                    'nilai' => $item->nilai,
                    'catatan' => $item->catatan,
                ],
            ];
        });

    /*
    |--------------------------------------------------------------------------
    | Kirim ke Vue
    |--------------------------------------------------------------------------
    */

    return Inertia::render('Tksi/Input/Create', [
        'periode' => $periode,
        'siswaTerpilih' => $siswa,
        'komponen' => $this->komponen(),
        'hasil' => $hasil,
    ]);
}
    /**
     * Simpan hasil TKSI
     */
    public function store(Request $request)
    {
        $periode = Periode::where('status', 'aktif')->first();

        if (!$periode) {
            return back()->withErrors([
                'periode' => 'Tidak ada periode aktif.',
            ]);
        }

        $validated = $request->validate([
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswas,id',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'hasil' => [
                'required',
                'array',
            ],

            'hasil.*.komponen' => [
                'required',
                'string',
                'max:50',
            ],

            'hasil.*.nilai' => [
                'required',
                'numeric',
            ],

            'hasil.*.catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa terdaftar pada periode aktif
        |--------------------------------------------------------------------------
        */

        $terdaftar = $periode->siswa()
            ->where('siswas.id', $validated['siswa_id'])
            ->exists();

        if (!$terdaftar) {
            return back()->withErrors([
                'siswa_id' => 'Siswa tidak terdaftar pada periode aktif.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan / update hasil
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($validated, $periode) {

            foreach ($validated['hasil'] as $item) {

                TksiHasil::updateOrCreate(
                    [
                        'periode_id' => $periode->id,
                        'siswa_id' => $validated['siswa_id'],
                        'komponen' => $item['komponen'],
                    ],
                    [
                        'tanggal' => $validated['tanggal'],
                        'nilai' => $item['nilai'],
                        'catatan' => $item['catatan'] ?? null,
                    ]
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Kembali ke index setelah berhasil
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('tksi.input.index')
            ->with('success', 'Hasil TKSI berhasil disimpan.');
    }


    /**
     * Komponen tes TKSI
     */
    private function komponen(): array
    {
        return [
            [
                'key' => 'hand_eye',
                'nama' => 'Hand and Eye Coordination',
                'satuan' => 'jumlah tangkapan',
                'deskripsi' => 'Jumlah tangkapan bola yang berhasil selama 30 detik.',
            ],

            [
                'key' => 'vertical_jump',
                'nama' => 'Vertical Jump',
                'satuan' => 'cm',
                'deskripsi' => 'Selisih tinggi raihan dan tinggi loncatan.',
            ],

            [
                'key' => 't_test',
                'nama' => 'T-Test',
                'satuan' => 'detik',
                'deskripsi' => 'Waktu terbaik penyelesaian lintasan T-Test.',
            ],

            [
                'key' => 'hand_touch',
                'nama' => 'Hand Touch Reaction',
                'satuan' => 'detik',
                'deskripsi' => 'Waktu terbaik dari aba-aba sampai menyentuh cone.',
            ],

            [
                'key' => 'dipping',
                'nama' => 'Dipping',
                'satuan' => 'jumlah',
                'deskripsi' => 'Jumlah pengulangan gerakan dipping yang benar.',
            ],

            [
                'key' => 'beep_test',
                'nama' => 'Beep Test',
                'satuan' => 'level/balikan',
                'deskripsi' => 'Hasil akhir berdasarkan level dan balikan beep test.',
            ],
        ];
    }
    /**
 * Laporan hasil TKSI berdasarkan periode
 */
public function report(Request $request)
{
    $periodeId = $request->query('periode_id');

    $periode = null;
    $siswa = collect();

    $statistik = [
        'total' => 0,
        'lengkap' => 0,
        'belum_lengkap' => 0,
    ];

    $periodeList = Periode::orderByDesc('tanggal_mulai')->get();

    if ($periodeId) {

        $periode = Periode::find($periodeId);

        if ($periode) {

            $dataSiswa = $periode->siswa()
                ->with('kelas.jurusan')
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get();

            $jumlahKomponen = count($this->komponen());

            $hasil = TksiHasil::where('periode_id', $periode->id)
                ->get()
                ->groupBy('siswa_id');

            $siswa = $dataSiswa->map(function ($student) use (
                $hasil,
                $jumlahKomponen
            ) {

                $hasilSiswa = $hasil->get(
                    $student->id,
                    collect()
                );

                return [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'nama' => $student->nama,
                    'jenis_kelamin' => $student->jenis_kelamin,

                    'kelas' => $student->kelas
                        ? [
                            'id' => $student->kelas->id,
                            'nama_kelas' => $student->kelas->nama_kelas,
                        ]
                        : null,

                    'jumlah_hasil' => $hasilSiswa->count(),

                    'lengkap' =>
                        $hasilSiswa->count() >= $jumlahKomponen,

                    'hasil' => $hasilSiswa->mapWithKeys(
                        function ($item) {
                            return [
                                $item->komponen => [
                                    'nilai' => $item->nilai,
                                    'catatan' => $item->catatan,
                                ],
                            ];
                        }
                    ),
                ];
            });

            $statistik = [
                'total' => $siswa->count(),

                'lengkap' => $siswa
                    ->where('lengkap', true)
                    ->count(),

                'belum_lengkap' => $siswa
                    ->where('lengkap', false)
                    ->count(),
            ];
        }
    }

    return Inertia::render('Tksi/Report/Index', [

        'periodeList' => $periodeList,

        'periode' => $periode,

        'siswa' => $siswa,

        'komponen' => $this->komponen(),

        'statistik' => $statistik,

    ]);
}
}
