<?php

namespace App\Http\Controllers\Klinik;

use App\Http\Controllers\Controller;
use App\Models\PemeriksaanBerkala;
use App\Models\Periode;
use App\Models\Siswa;
use App\Services\NotificationService;
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
     * Aturan:
     *
     * BULAN 1 - 3
     * - Berkala 1 : OPEN
     * - Berkala 2 : CLOSED
     *
     * BULAN 4 - 6
     * - Berkala 1 : VIEW ONLY
     * - Berkala 2 : OPEN
     */
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();

        /**
         * ----------------------------------------------------------
         * Belum ada periode aktif
         * ----------------------------------------------------------
         */
        if (!$periodeAktif) {
            return Inertia::render(
                'Klinik/Kesehatan/PemeriksaanBerkala/Index',
                [
                    'periode' => null,
                    'siswas' => [],
                ]
            );
        }

        /**
         * ----------------------------------------------------------
         * Ambil status akses pemeriksaan
         * ----------------------------------------------------------
         */
        $fasePemeriksaan = $periodeAktif->fasePemeriksaan();

        $aksesBerkala1 = $periodeAktif
            ->statusAksesPemeriksaan('berkala_1');

        $aksesBerkala2 = $periodeAktif
            ->statusAksesPemeriksaan('berkala_2');

        /**
         * ----------------------------------------------------------
         * Ambil siswa pada periode aktif
         * ----------------------------------------------------------
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
                        ])
                        ->with('pemeriksa')
                        ->latest('tanggal_pemeriksaan');
                },
            ])
            ->orderBy('nama')
            ->get();

        /**
         * ----------------------------------------------------------
         * Bentuk data untuk Vue
         * ----------------------------------------------------------
         */
        $siswas = $siswas->map(function ($siswa) {

            $berkala1 = $siswa->pemeriksaanBerkala
                ->firstWhere('jenis_pemeriksaan', 'berkala_1');

            $berkala2 = $siswa->pemeriksaanBerkala
                ->firstWhere('jenis_pemeriksaan', 'berkala_2');

            return [

                // ==================================================
                // DATA SISWA
                // ==================================================

                'id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,

                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir,

                'jenis_kelamin' => $siswa->jenis_kelamin,
                'agama' => $siswa->agama,

                // ==================================================
                // KELAS
                // ==================================================

                'kelas' => $siswa->kelas
                    ? [
                        'id' => $siswa->kelas->id,
                        'nama_kelas' => $siswa->kelas->nama_kelas,
                        'tingkat' => $siswa->kelas->tingkat ?? null,

                        'jurusan' => $siswa->kelas->jurusan
                            ? [
                                'id' => $siswa->kelas->jurusan->id,
                                'nama_jurusan' =>
                                    $siswa->kelas->jurusan->nama_jurusan,
                            ]
                            : null,
                    ]
                    : null,

                // ==================================================
                // BERKALA 1
                // ==================================================

                'berkala_1' => $berkala1
                    ? [

                        'id' => $berkala1->id,

                        'jenis_pemeriksaan' =>
                            $berkala1->jenis_pemeriksaan,

                        'tanggal_pemeriksaan' =>
                            $berkala1->tanggal_pemeriksaan,

                        'status' =>
                            $berkala1->status,

                        // ------------------------------
                        // ANTROPOMETRI
                        // ------------------------------

                        'berat_badan' =>
                            $berkala1->berat_badan,

                        'tinggi_badan' =>
                            $berkala1->tinggi_badan,

                        'imt' =>
                            $berkala1->imt,

                        // ------------------------------
                        // TANDA VITAL
                        // ------------------------------

                        'tekanan_darah' =>
                            $berkala1->tekanan_darah,

                        'denyut_nadi' =>
                            $berkala1->denyut_nadi,

                        'suhu_tubuh' =>
                            $berkala1->suhu_tubuh,

                        'saturasi_oksigen' =>
                            $berkala1->saturasi_oksigen,

                        // ------------------------------
                        // PEMERIKSAAN FISIK
                        // ------------------------------

                        'mata' =>
                            $berkala1->mata,

                        'telinga' =>
                            $berkala1->telinga,

                        'gigi_mulut' =>
                            $berkala1->gigi_mulut,

                        'kondisi_umum' =>
                            $berkala1->kondisi_umum,

                        // ------------------------------
                        // KEBERSIHAN
                        // ------------------------------

                        'kebersihan_rambut' =>
                            $berkala1->kebersihan_rambut,

                        'kebersihan_wajah' =>
                            $berkala1->kebersihan_wajah,

                        'kebersihan_telinga' =>
                            $berkala1->kebersihan_telinga,

                        'kebersihan_hidung' =>
                            $berkala1->kebersihan_hidung,

                        'kebersihan_mulut_gigi' =>
                            $berkala1->kebersihan_mulut_gigi,

                        'kebersihan_tangan_kuku' =>
                            $berkala1->kebersihan_tangan_kuku,

                        'kebersihan_kulit_badan' =>
                            $berkala1->kebersihan_kulit_badan,

                        'kebersihan_kaki_kuku' =>
                            $berkala1->kebersihan_kaki_kuku,

                        // ------------------------------
                        // HASIL PEMERIKSAAN
                        // ------------------------------

                        'keluhan' =>
                            $berkala1->keluhan,

                        'hasil_pemeriksaan' =>
                            $berkala1->hasil_pemeriksaan,

                        'rekomendasi' =>
                            $berkala1->rekomendasi,

                        // ------------------------------
                        // CATATAN
                        // ------------------------------

                        'catatan' =>
                            $berkala1->catatan,

                        'pemeriksa' => $berkala1->pemeriksa
                            ? [
                                'id' => $berkala1->pemeriksa->id,
                                'name' => $berkala1->pemeriksa->name,
                            ]
                            : null,
                    ]
                    : null,

                // ==================================================
                // BERKALA 2
                // ==================================================

                'berkala_2' => $berkala2
                    ? [

                        'id' => $berkala2->id,

                        'jenis_pemeriksaan' =>
                            $berkala2->jenis_pemeriksaan,

                        'tanggal_pemeriksaan' =>
                            $berkala2->tanggal_pemeriksaan,

                        'status' =>
                            $berkala2->status,

                        // ------------------------------
                        // ANTROPOMETRI
                        // ------------------------------

                        'berat_badan' =>
                            $berkala2->berat_badan,

                        'tinggi_badan' =>
                            $berkala2->tinggi_badan,

                        'imt' =>
                            $berkala2->imt,

                        // ------------------------------
                        // TANDA VITAL
                        // ------------------------------

                        'tekanan_darah' =>
                            $berkala2->tekanan_darah,

                        'denyut_nadi' =>
                            $berkala2->denyut_nadi,

                        'suhu_tubuh' =>
                            $berkala2->suhu_tubuh,

                        'saturasi_oksigen' =>
                            $berkala2->saturasi_oksigen,

                        // ------------------------------
                        // PEMERIKSAAN FISIK
                        // ------------------------------

                        'mata' =>
                            $berkala2->mata,

                        'telinga' =>
                            $berkala2->telinga,

                        'gigi_mulut' =>
                            $berkala2->gigi_mulut,

                        'kondisi_umum' =>
                            $berkala2->kondisi_umum,

                        // ------------------------------
                        // KEBERSIHAN
                        // ------------------------------

                        'kebersihan_rambut' =>
                            $berkala2->kebersihan_rambut,

                        'kebersihan_wajah' =>
                            $berkala2->kebersihan_wajah,

                        'kebersihan_telinga' =>
                            $berkala2->kebersihan_telinga,

                        'kebersihan_hidung' =>
                            $berkala2->kebersihan_hidung,

                        'kebersihan_mulut_gigi' =>
                            $berkala2->kebersihan_mulut_gigi,

                        'kebersihan_tangan_kuku' =>
                            $berkala2->kebersihan_tangan_kuku,

                        'kebersihan_kulit_badan' =>
                            $berkala2->kebersihan_kulit_badan,

                        'kebersihan_kaki_kuku' =>
                            $berkala2->kebersihan_kaki_kuku,

                        // ------------------------------
                        // HASIL PEMERIKSAAN
                        // ------------------------------

                        'keluhan' =>
                            $berkala2->keluhan,

                        'hasil_pemeriksaan' =>
                            $berkala2->hasil_pemeriksaan,

                        'rekomendasi' =>
                            $berkala2->rekomendasi,

                        // ------------------------------
                        // CATATAN
                        // ------------------------------

                        'catatan' =>
                            $berkala2->catatan,

                        'pemeriksa' => $berkala2->pemeriksa
                            ? [
                                'id' => $berkala2->pemeriksa->id,
                                'name' => $berkala2->pemeriksa->name,
                            ]
                            : null,
                    ]
                    : null,
            ];
        });

        /**
         * ----------------------------------------------------------
         * Kirim data ke Vue
         * ----------------------------------------------------------
         */
        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Index',
            [
                'periode' => [
                    'id' =>
                        $periodeAktif->id,

                    'nama_periode' =>
                        $periodeAktif->nama_periode,

                    'tanggal_mulai' =>
                        $periodeAktif->tanggal_mulai,

                    'tanggal_selesai' =>
                        $periodeAktif->tanggal_selesai,

                    'status' =>
                        $periodeAktif->status,

                    'fase_pemeriksaan' =>
                        $fasePemeriksaan,

                    'berkala_1' => [
                        'akses' =>
                            $aksesBerkala1,
                    ],

                    'berkala_2' => [
                        'akses' =>
                            $aksesBerkala2,
                    ],
                ],

                'siswas' => $siswas,
            ]
        );
    }


    /**
     * ============================================================
     * FORM TAMBAH PEMERIKSAAN
     * ============================================================
     */
    public function create(
        Siswa $siswa,
        string $jenis
    ) {
        $periodeAktif = Periode::where('status', 'aktif')
            ->firstOrFail();

        $siswa = $periodeAktif->siswa()
            ->where('siswas.id', $siswa->id)
            ->with([
                'kelas.jurusan',
            ])
            ->firstOrFail();

        $jenis = $this->normalizeJenis($jenis);

        $akses = $periodeAktif
            ->statusAksesPemeriksaan($jenis);

        if ($akses !== 'open') {

            abort(
                403,
                $jenis === 'berkala_1'
                    ? 'Berkala 1 sudah tidak dapat diisi.'
                    : 'Berkala 2 belum dibuka.'
            );
        }

        $pemeriksaan = PemeriksaanBerkala::where(
            'periode_id',
            $periodeAktif->id
        )
            ->where(
                'siswa_id',
                $siswa->id
            )
            ->where(
                'jenis_pemeriksaan',
                $jenis
            )
            ->latest()
            ->first();

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Create',
            [
                'siswa' =>
                    $siswa,

                'periode' => [
                    'id' =>
                        $periodeAktif->id,

                    'nama_periode' =>
                        $periodeAktif->nama_periode,

                    'tanggal_mulai' =>
                        $periodeAktif->tanggal_mulai,

                    'tanggal_selesai' =>
                        $periodeAktif->tanggal_selesai,

                    'fase_pemeriksaan' =>
                        $periodeAktif->fasePemeriksaan(),
                ],

                'jenis' =>
                    $jenis,

                'pemeriksaan' =>
                    $pemeriksaan,
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
        $periodeAktif = Periode::where(
            'status',
            'aktif'
        )->firstOrFail();

        $siswa = $periodeAktif->siswa()
            ->where(
                'siswas.id',
                $siswa->id
            )
            ->firstOrFail();

        $jenis = $this->normalizeJenis($jenis);

        $akses = $periodeAktif
            ->statusAksesPemeriksaan($jenis);

        if ($akses !== 'open') {

            abort(
                403,
                $jenis === 'berkala_1'
                    ? 'Berkala 1 sudah tidak dapat diisi.'
                    : 'Berkala 2 belum dibuka.'
            );
        }

        /**
         * ----------------------------------------------------------
         * Validasi
         * ----------------------------------------------------------
         */
        $validated = $request->validate([

            // ======================================================
            // IDENTITAS
            // ======================================================

            'tanggal_pemeriksaan' => [
                'required',
                'date',
            ],

            // ======================================================
            // ANTROPOMETRI
            // ======================================================

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

            // ======================================================
            // TANDA VITAL
            // ======================================================

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

            'saturasi_oksigen' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            // ======================================================
            // PEMERIKSAAN FISIK
            // ======================================================

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

            // ======================================================
            // KEBERSIHAN
            // ======================================================

            'kebersihan_rambut' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_wajah' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_telinga' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_hidung' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_mulut_gigi' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_tangan_kuku' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_kulit_badan' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_kaki_kuku' => [
                'nullable',
                'string',
                'max:50',
            ],

            // ======================================================
            // HASIL
            // ======================================================

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

            // ======================================================
            // STATUS
            // ======================================================

            'status' => [
                'required',
                'in:belum,selesai',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /**
         * ----------------------------------------------------------
         * Simpan / Update
         * ----------------------------------------------------------
         */
        PemeriksaanBerkala::updateOrCreate(
            [
                'periode_id' =>
                    $periodeAktif->id,

                'siswa_id' =>
                    $siswa->id,

                'jenis_pemeriksaan' =>
                    $jenis,
            ],
            [

                // ==================================================
                // IDENTITAS
                // ==================================================

                'tanggal_pemeriksaan' =>
                    $validated['tanggal_pemeriksaan'],

                // ==================================================
                // ANTROPOMETRI
                // ==================================================

                'berat_badan' =>
                    $validated['berat_badan'] ?? null,

                'tinggi_badan' =>
                    $validated['tinggi_badan'] ?? null,

                'imt' =>
                    $validated['imt'] ?? null,

                // ==================================================
                // TANDA VITAL
                // ==================================================

                'tekanan_darah' =>
                    $validated['tekanan_darah'] ?? null,

                'denyut_nadi' =>
                    $validated['denyut_nadi'] ?? null,

                'suhu_tubuh' =>
                    $validated['suhu_tubuh'] ?? null,

                'saturasi_oksigen' =>
                    $validated['saturasi_oksigen'] ?? null,

                // ==================================================
                // PEMERIKSAAN FISIK
                // ==================================================

                'mata' =>
                    $validated['mata'] ?? null,

                'telinga' =>
                    $validated['telinga'] ?? null,

                'gigi_mulut' =>
                    $validated['gigi_mulut'] ?? null,

                'kondisi_umum' =>
                    $validated['kondisi_umum'] ?? null,

                // ==================================================
                // KEBERSIHAN
                // ==================================================

                'kebersihan_rambut' =>
                    $validated['kebersihan_rambut'] ?? null,

                'kebersihan_wajah' =>
                    $validated['kebersihan_wajah'] ?? null,

                'kebersihan_telinga' =>
                    $validated['kebersihan_telinga'] ?? null,

                'kebersihan_hidung' =>
                    $validated['kebersihan_hidung'] ?? null,

                'kebersihan_mulut_gigi' =>
                    $validated['kebersihan_mulut_gigi'] ?? null,

                'kebersihan_tangan_kuku' =>
                    $validated['kebersihan_tangan_kuku'] ?? null,

                'kebersihan_kulit_badan' =>
                    $validated['kebersihan_kulit_badan'] ?? null,

                'kebersihan_kaki_kuku' =>
                    $validated['kebersihan_kaki_kuku'] ?? null,

                // ==================================================
                // HASIL
                // ==================================================

                'keluhan' =>
                    $validated['keluhan'] ?? null,

                'hasil_pemeriksaan' =>
                    $validated['hasil_pemeriksaan'] ?? null,

                'rekomendasi' =>
                    $validated['rekomendasi'] ?? null,

                // ==================================================
                // STATUS
                // ==================================================

                'status' =>
                    $validated['status'],

                'catatan' =>
                    $validated['catatan'] ?? null,

                // ==================================================
                // PEMERIKSA
                // ==================================================

                'pemeriksa_id' =>
                    Auth::id(),
            ]
        );

        NotificationService::toRole(
            'admin',
            'Pemeriksaan Berkala Baru',
            "Pemeriksaan {$this->labelJenis($jenis)} untuk siswa {$siswa->nama} telah selesai dilakukan oleh Klinik.",
            'info',
            route('admin.pemeriksaan.index')
        );

        NotificationService::toRole(
            'tksi',
            'Data Kesehatan Siswa Diperbarui',
            "Pemeriksaan {$this->labelJenis($jenis)} siswa {$siswa->nama} telah diperbarui. Silakan periksa bila diperlukan.",
            'info',
            route('tksi.input.index')
        );


        return redirect()
            ->route(
                'klinik.kesehatan.pemeriksaan.index'
            )
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
    public function show(
        PemeriksaanBerkala $pemeriksaanBerkala
    ) {
        $periodeAktif = Periode::where(
            'status',
            'aktif'
        )->firstOrFail();

        if (
            $pemeriksaanBerkala->periode_id !==
            $periodeAktif->id
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
                'pemeriksaan' =>
                    $pemeriksaanBerkala,
            ]
        );
    }


    /**
     * ============================================================
     * FORM EDIT
     * ============================================================
     */
    public function edit(
        PemeriksaanBerkala $pemeriksaanBerkala
    ) {
        $periodeAktif = Periode::where(
            'status',
            'aktif'
        )->firstOrFail();

        if (
            $pemeriksaanBerkala->periode_id !==
            $periodeAktif->id
        ) {
            abort(404);
        }

        $akses = $periodeAktif
            ->statusAksesPemeriksaan(
                $pemeriksaanBerkala->jenis_pemeriksaan
            );

        if ($akses !== 'open') {

            abort(
                403,
                'Pemeriksaan ' .
                $this->labelJenis(
                    $pemeriksaanBerkala->jenis_pemeriksaan
                ) .
                ' sudah tidak dapat diedit.'
            );
        }

        $pemeriksaanBerkala->load([
            'siswa.kelas.jurusan',
            'periode',
            'pemeriksa',
        ]);

        return Inertia::render(
            'Klinik/Kesehatan/PemeriksaanBerkala/Edit',
            [
                'pemeriksaan' =>
                    $pemeriksaanBerkala,
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
        $periodeAktif = Periode::where(
            'status',
            'aktif'
        )->firstOrFail();

        if (
            $pemeriksaanBerkala->periode_id !==
            $periodeAktif->id
        ) {
            abort(404);
        }

        $akses = $periodeAktif
            ->statusAksesPemeriksaan(
                $pemeriksaanBerkala->jenis_pemeriksaan
            );

        if ($akses !== 'open') {

            abort(
                403,
                'Pemeriksaan ' .
                $this->labelJenis(
                    $pemeriksaanBerkala->jenis_pemeriksaan
                ) .
                ' sudah tidak dapat diperbarui.'
            );
        }

        /**
         * ----------------------------------------------------------
         * Validasi
         * ----------------------------------------------------------
         */
        $validated = $request->validate([

            // ======================================================
            // IDENTITAS
            // ======================================================

            'tanggal_pemeriksaan' => [
                'required',
                'date',
            ],

            // ======================================================
            // ANTROPOMETRI
            // ======================================================

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

            // ======================================================
            // TANDA VITAL
            // ======================================================

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

            'saturasi_oksigen' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            // ======================================================
            // PEMERIKSAAN FISIK
            // ======================================================

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

            // ======================================================
            // KEBERSIHAN
            // ======================================================

            'kebersihan_rambut' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_wajah' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_telinga' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_hidung' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_mulut_gigi' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_tangan_kuku' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_kulit_badan' => [
                'nullable',
                'string',
                'max:50',
            ],

            'kebersihan_kaki_kuku' => [
                'nullable',
                'string',
                'max:50',
            ],

            // ======================================================
            // HASIL
            // ======================================================

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

            // ======================================================
            // STATUS
            // ======================================================

            'status' => [
                'required',
                'in:belum,selesai',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        /**
         * ----------------------------------------------------------
         * Update
         * ----------------------------------------------------------
         */
        $pemeriksaanBerkala->update([

            // ======================================================
            // IDENTITAS
            // ======================================================

            'tanggal_pemeriksaan' =>
                $validated['tanggal_pemeriksaan'],

            // ======================================================
            // ANTROPOMETRI
            // ======================================================

            'berat_badan' =>
                $validated['berat_badan'] ?? null,

            'tinggi_badan' =>
                $validated['tinggi_badan'] ?? null,

            'imt' =>
                $validated['imt'] ?? null,

            // ======================================================
            // TANDA VITAL
            // ======================================================

            'tekanan_darah' =>
                $validated['tekanan_darah'] ?? null,

            'denyut_nadi' =>
                $validated['denyut_nadi'] ?? null,

            'suhu_tubuh' =>
                $validated['suhu_tubuh'] ?? null,

            'saturasi_oksigen' =>
                $validated['saturasi_oksigen'] ?? null,

            // ======================================================
            // PEMERIKSAAN FISIK
            // ======================================================

            'mata' =>
                $validated['mata'] ?? null,

            'telinga' =>
                $validated['telinga'] ?? null,

            'gigi_mulut' =>
                $validated['gigi_mulut'] ?? null,

            'kondisi_umum' =>
                $validated['kondisi_umum'] ?? null,

            // ======================================================
            // KEBERSIHAN
            // ======================================================

            'kebersihan_rambut' =>
                $validated['kebersihan_rambut'] ?? null,

            'kebersihan_wajah' =>
                $validated['kebersihan_wajah'] ?? null,

            'kebersihan_telinga' =>
                $validated['kebersihan_telinga'] ?? null,

            'kebersihan_hidung' =>
                $validated['kebersihan_hidung'] ?? null,

            'kebersihan_mulut_gigi' =>
                $validated['kebersihan_mulut_gigi'] ?? null,

            'kebersihan_tangan_kuku' =>
                $validated['kebersihan_tangan_kuku'] ?? null,

            'kebersihan_kulit_badan' =>
                $validated['kebersihan_kulit_badan'] ?? null,

            'kebersihan_kaki_kuku' =>
                $validated['kebersihan_kaki_kuku'] ?? null,

            // ======================================================
            // HASIL
            // ======================================================

            'keluhan' =>
                $validated['keluhan'] ?? null,

            'hasil_pemeriksaan' =>
                $validated['hasil_pemeriksaan'] ?? null,

            'rekomendasi' =>
                $validated['rekomendasi'] ?? null,

            // ======================================================
            // STATUS
            // ======================================================

            'status' =>
                $validated['status'],

            'catatan' =>
                $validated['catatan'] ?? null,

            // ======================================================
            // PEMERIKSA
            // ======================================================

            'pemeriksa_id' =>
                Auth::id(),
        ]);

        $pemeriksaanBerkala->load('siswa');

        NotificationService::toRole(
            'admin',
            'Pemeriksaan Berkala Diperbarui',
            "Pemeriksaan {$this->labelJenis($pemeriksaanBerkala->jenis_pemeriksaan)} untuk siswa {$pemeriksaanBerkala->siswa->nama} telah diperbarui.",
            'info',
            route('admin.pemeriksaan.show', $pemeriksaanBerkala->id)
        );

        NotificationService::toRole(
            'tksi',
            'Data Kesehatan Siswa Diperbarui',
            "Data pemeriksaan siswa {$pemeriksaanBerkala->siswa->nama} telah diperbarui oleh Klinik.",
            'info',
            route('tksi.input.index')
        );

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
     */
    private function normalizeJenis(
        string $jenis
    ): string {

        $jenis = strtolower(
            trim($jenis)
        );

        return match ($jenis) {

            '1',
            'berkala1',
            'berkala-1',
            'berkala 1',
            'berkala_1'
                => 'berkala_1',

            '2',
            'berkala2',
            'berkala-2',
            'berkala 2',
            'berkala_2'
                => 'berkala_2',

            default =>
                abort(404),
        };
    }


    /**
     * ============================================================
     * LABEL JENIS
     * ============================================================
     */
    private function labelJenis(
        string $jenis
    ): string {

        return match ($jenis) {

            'berkala_1'
                => 'Berkala 1',

            'berkala_2'
                => 'Berkala 2',

            default =>
                ucfirst($jenis),
        };
    }
}