<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\PemeriksaanBerkala;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PemeriksaanBerkalaController extends Controller
{
    /**
     * ============================================================
     * DAFTAR PEMERIKSAAN BERKALA
     * ============================================================
     *
     * Menampilkan semua siswa pada periode aktif.
     *
     * Setiap siswa memiliki:
     * - Berkala 1
     * - Berkala 2
     */
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();

        if (!$periodeAktif) {
            return Inertia::render(
                'Klinik/Kesehatan/PemeriksaanBerkala/Index',
                [
                    'periode' => null,
                    'siswas' => [],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil siswa dari periode aktif
        |--------------------------------------------------------------------------
        */
        $siswas = $periodeAktif->siswa()
            ->with([
                'kelas.jurusan',

                'pemeriksaanBerkala' => function ($query) use ($periodeAktif) {
                    $query
                        ->where('periode_id', $periodeAktif->id)
                        ->whereIn('jenis_pemeriksaan', [
                            'berkala_1',
                            'berkala_2',
                            'Berkala 1',
                            'Berkala 2',
                        ])
                        ->latest('tanggal_pemeriksaan');
                },
            ])
            ->orderBy('nama')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Bentuk data agar Vue lebih mudah
        |--------------------------------------------------------------------------
        */
        $siswas = $siswas->map(function ($siswa) {

            $berkala1 = $siswa->pemeriksaanBerkala
                ->first(function ($pemeriksaan) {
                    return in_array(
                        strtolower(trim($pemeriksaan->jenis_pemeriksaan)),
                        [
                            'berkala_1',
                            'berkala 1',
                        ]
                    );
                });

            $berkala2 = $siswa->pemeriksaanBerkala
                ->first(function ($pemeriksaan) {
                    return in_array(
                        strtolower(trim($pemeriksaan->jenis_pemeriksaan)),
                        [
                            'berkala_2',
                            'berkala 2',
                        ]
                    );
                });

            return [
                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,

                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir,

                'jenis_kelamin' => $siswa->jenis_kelamin,
                'agama' => $siswa->agama,

                'kelas' => $siswa->kelas
                    ? [
                        'id' => $siswa->kelas->id,
                        'nama_kelas' => $siswa->kelas->nama_kelas,
                        'jurusan' => $siswa->kelas->jurusan
                            ? [
                                'id' => $siswa->kelas->jurusan->id,
                                'nama_jurusan' => $siswa->kelas->jurusan->nama_jurusan,
                            ]
                            : null,
                    ]
                    : null,

                'berkala_1' => $berkala1
                    ? [
                        'id' => $berkala1->id,
                        'jenis_pemeriksaan' => $berkala1->jenis_pemeriksaan,
                        'tanggal_pemeriksaan' => $berkala1->tanggal_pemeriksaan,
                        'status' => $berkala1->status,
                        'hasil' => $berkala1->hasil,
                        'catatan' => $berkala1->catatan,
                    ]
                    : null,

                'berkala_2' => $berkala2
                    ? [
                        'id' => $berkala2->id,
                        'jenis_pemeriksaan' => $berkala2->jenis_pemeriksaan,
                        'tanggal_pemeriksaan' => $berkala2->tanggal_pemeriksaan,
                        'status' => $berkala2->status,
                        'hasil' => $berkala2->hasil,
                        'catatan' => $berkala2->catatan,
                    ]
                    : null,
            ];
        });

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Index',
            [
                'periode' => [
                    'id' => $periodeAktif->id,
                    'nama_periode' => $periodeAktif->nama_periode,
                    'tanggal_mulai' => $periodeAktif->tanggal_mulai,
                    'tanggal_selesai' => $periodeAktif->tanggal_selesai,
                    'status' => $periodeAktif->status,
                ],

                'siswas' => $siswas,
            ]
        );
    }


    /**
     * ============================================================
     * FORM TAMBAH PEMERIKSAAN
     * ============================================================
     *
     * URL:
     * /klinik/kesehatan/pemeriksaan-berkala/{siswa}/{jenis}
     *
     * Contoh:
     * /klinik/kesehatan/pemeriksaan-berkala/12/berkala_1
     */
    public function create(Siswa $siswa, string $jenis)
    {
        $periodeAktif = Periode::where('status', 'aktif')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa memang terdaftar pada periode aktif
        |--------------------------------------------------------------------------
        */
        $siswa = $periodeAktif->siswa()
            ->where('siswas.id', $siswa->id)
            ->with([
                'kelas.jurusan',
            ])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Normalisasi jenis pemeriksaan
        |--------------------------------------------------------------------------
        */
        $jenis = $this->normalizeJenis($jenis);

        /*
        |--------------------------------------------------------------------------
        | Cek apakah pemeriksaan sudah pernah dibuat
        |--------------------------------------------------------------------------
        */
        $pemeriksaan = PemeriksaanBerkala::where(
            'periode_id',
            $periodeAktif->id
        )
            ->where('siswa_id', $siswa->id)
            ->where('jenis_pemeriksaan', $jenis)
            ->latest()
            ->first();

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Create',
            [
                'siswa' => $siswa,

                'periode' => [
                    'id' => $periodeAktif->id,
                    'nama_periode' => $periodeAktif->nama_periode,
                ],

                'jenis' => $jenis,

                'pemeriksaan' => $pemeriksaan,
            ]
        );
    }


    /**
     * ============================================================
     * SIMPAN PEMERIKSAAN
     * ============================================================
     */
    public function store(
    Request $request,
    Siswa $siswa,
    string $jenis
) {
    $periodeAktif = Periode::where('status', 'aktif')
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | Pastikan siswa ada pada periode aktif
    |--------------------------------------------------------------------------
    */
    $siswa = $periodeAktif->siswa()
        ->where('siswas.id', $siswa->id)
        ->firstOrFail();

    $jenis = $this->normalizeJenis($jenis);

    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */
    $validated = $request->validate([

        // IDENTITAS
        'tanggal_pemeriksaan' => [
            'required',
            'date',
        ],

        // ANTROPOMETRI
        'berat_badan' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'tinggi_badan' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'imt' => [
            'nullable',
            'numeric',
            'min:0',
            'max:999',
        ],

        // TANDA VITAL
        'tekanan_darah' => [
            'nullable',
            'string',
            'max:20',
        ],

        'denyut_nadi' => [
            'nullable',
            'integer',
            'min:0',
        ],

        'suhu_tubuh' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        // PEMERIKSAAN FISIK
        'mata' => [
            'nullable',
            'string',
            'max:50',
        ],

        'telinga' => [
            'nullable',
            'string',
            'max:50',
        ],

        'gigi_mulut' => [
            'nullable',
            'string',
            'max:50',
        ],

        'kondisi_umum' => [
            'nullable',
            'string',
            'max:50',
        ],

        // HASIL
        'keluhan' => [
            'nullable',
            'string',
        ],

        'hasil_pemeriksaan' => [
            'nullable',
            'string',
        ],

        'rekomendasi' => [
            'nullable',
            'string',
        ],

        // STATUS
        'status' => [
            'required',
            'in:belum,selesai',
        ],

        'catatan' => [
            'nullable',
            'string',
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Simpan / Update
    |--------------------------------------------------------------------------
    */
    PemeriksaanBerkala::updateOrCreate(
        [
            'periode_id' => $periodeAktif->id,
            'siswa_id' => $siswa->id,
            'jenis_pemeriksaan' => $jenis,
        ],
        [
            'tanggal_pemeriksaan' =>
                $validated['tanggal_pemeriksaan'],

            // ANTROPOMETRI
            'berat_badan' =>
                $validated['berat_badan'] ?? null,

            'tinggi_badan' =>
                $validated['tinggi_badan'] ?? null,

            'imt' =>
                $validated['imt'] ?? null,

            // TANDA VITAL
            'tekanan_darah' =>
                $validated['tekanan_darah'] ?? null,

            'denyut_nadi' =>
                $validated['denyut_nadi'] ?? null,

            'suhu_tubuh' =>
                $validated['suhu_tubuh'] ?? null,

            // PEMERIKSAAN FISIK
            'mata' =>
                $validated['mata'] ?? null,

            'telinga' =>
                $validated['telinga'] ?? null,

            'gigi_mulut' =>
                $validated['gigi_mulut'] ?? null,

            'kondisi_umum' =>
                $validated['kondisi_umum'] ?? null,

            // HASIL
            'keluhan' =>
                $validated['keluhan'] ?? null,

            'hasil_pemeriksaan' =>
                $validated['hasil_pemeriksaan'] ?? null,

            'rekomendasi' =>
                $validated['rekomendasi'] ?? null,

            // STATUS
            'status' =>
                $validated['status'],

            'catatan' =>
                $validated['catatan'] ?? null,

            // PEMERIKSA
            'pemeriksa_id' =>
                Auth::id(),
        ]
    );

    return redirect()
        ->route('klinik.kesehatan.pemeriksaan.index')
        ->with(
            'success',
            'Pemeriksaan ' .
            $this->labelJenis($jenis) .
            ' berhasil disimpan.'
        );
}

    /**
     * ============================================================
     * DETAIL PEMERIKSAAN
     * ============================================================
     */
    public function show(PemeriksaanBerkala $pemeriksaanBerkala)
    {
        $periodeAktif = Periode::where('status', 'aktif')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Pastikan pemeriksaan milik periode aktif
        |--------------------------------------------------------------------------
        */
        if (
            $pemeriksaanBerkala->periode_id !== $periodeAktif->id
        ) {
            abort(404);
        }

        $pemeriksaanBerkala->load([
            'siswa.kelas.jurusan',
            'periode',
            'pemeriksa',
        ]);

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Show',
            [
                'pemeriksaan' => $pemeriksaanBerkala,
            ]
        );
    }


    /**
     * ============================================================
     * FORM EDIT
     * ============================================================
     */
    public function edit(PemeriksaanBerkala $pemeriksaanBerkala)
    {
        $periodeAktif = Periode::where('status', 'aktif')
            ->firstOrFail();

        if (
            $pemeriksaanBerkala->periode_id !== $periodeAktif->id
        ) {
            abort(404);
        }

        $pemeriksaanBerkala->load([
            'siswa.kelas.jurusan',
            'periode',
        ]);

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Edit',
            [
                'pemeriksaan' => $pemeriksaanBerkala,
            ]
        );
    }


    /**
     * ============================================================
     * UPDATE PEMERIKSAAN
     * ============================================================
     */
    public function update(
    Request $request,
    PemeriksaanBerkala $pemeriksaanBerkala
) {
    $periodeAktif = Periode::where('status', 'aktif')
        ->firstOrFail();

    if (
        $pemeriksaanBerkala->periode_id !==
        $periodeAktif->id
    ) {
        abort(404);
    }

    $validated = $request->validate([

        // IDENTITAS
        'tanggal_pemeriksaan' => [
            'required',
            'date',
        ],

        // ANTROPOMETRI
        'berat_badan' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'tinggi_badan' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'imt' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        // TANDA VITAL
        'tekanan_darah' => [
            'nullable',
            'string',
            'max:20',
        ],

        'denyut_nadi' => [
            'nullable',
            'integer',
            'min:0',
        ],

        'suhu_tubuh' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        // PEMERIKSAAN FISIK
        'mata' => [
            'nullable',
            'string',
            'max:50',
        ],

        'telinga' => [
            'nullable',
            'string',
            'max:50',
        ],

        'gigi_mulut' => [
            'nullable',
            'string',
            'max:50',
        ],

        'kondisi_umum' => [
            'nullable',
            'string',
            'max:50',
        ],

        // HASIL
        'keluhan' => [
            'nullable',
            'string',
        ],

        'hasil_pemeriksaan' => [
            'nullable',
            'string',
        ],

        'rekomendasi' => [
            'nullable',
            'string',
        ],

        // STATUS
        'status' => [
            'required',
            'in:belum,selesai',
        ],

        'catatan' => [
            'nullable',
            'string',
        ],
    ]);

    $pemeriksaanBerkala->update([

        'tanggal_pemeriksaan' =>
            $validated['tanggal_pemeriksaan'],

        // ANTROPOMETRI
        'berat_badan' =>
            $validated['berat_badan'] ?? null,

        'tinggi_badan' =>
            $validated['tinggi_badan'] ?? null,

        'imt' =>
            $validated['imt'] ?? null,

        // TANDA VITAL
        'tekanan_darah' =>
            $validated['tekanan_darah'] ?? null,

        'denyut_nadi' =>
            $validated['denyut_nadi'] ?? null,

        'suhu_tubuh' =>
            $validated['suhu_tubuh'] ?? null,

        // PEMERIKSAAN FISIK
        'mata' =>
            $validated['mata'] ?? null,

        'telinga' =>
            $validated['telinga'] ?? null,

        'gigi_mulut' =>
            $validated['gigi_mulut'] ?? null,

        'kondisi_umum' =>
            $validated['kondisi_umum'] ?? null,

        // HASIL
        'keluhan' =>
            $validated['keluhan'] ?? null,

        'hasil_pemeriksaan' =>
            $validated['hasil_pemeriksaan'] ?? null,

        'rekomendasi' =>
            $validated['rekomendasi'] ?? null,

        // STATUS
        'status' =>
            $validated['status'],

        'catatan' =>
            $validated['catatan'] ?? null,

        // PEMERIKSA
        'pemeriksa_id' =>
            Auth::id(),
    ]);

    return redirect()
        ->route(
            'klinik.kesehatan.pemeriksaan.show',
            $pemeriksaanBerkala
        )
        ->with(
            'success',
            'Data pemeriksaan berhasil diperbarui.'
        );
}

    /**
     * ============================================================
     * NORMALISASI JENIS
     * ============================================================
     *
     * Semua variasi:
     * berkala1
     * berkala_1
     * Berkala 1
     *
     * akan disimpan sebagai:
     * berkala_1
     */
    private function normalizeJenis(string $jenis): string
{
    $jenis = strtolower(trim($jenis));

    return match ($jenis) {
        '1',
        'berkala1',
        'berkala-1',
        'berkala 1',
        'berkala_1' => 'berkala_1',

        '2',
        'berkala2',
        'berkala-2',
        'berkala 2',
        'berkala_2' => 'berkala_2',

        default => abort(404),
    };
}


    /**
     * ============================================================
     * LABEL JENIS
     * ============================================================
     */
    private function labelJenis(string $jenis): string
    {
        return match ($jenis) {
            'berkala_1' => 'Berkala 1',
            'berkala_2' => 'Berkala 2',
            default => ucfirst($jenis),
        };
    }
}