<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\PemeriksaanBerkala;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================
        // USER
        // =====================================================

        $admin = User::updateOrCreate(
            ['email' => 'admin@sikes.test'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        $klinik = User::updateOrCreate(
            ['email' => 'klinik@sikes.test'],
            [
                'name' => 'Petugas Klinik',
                'role' => 'klinik',
                'password' => Hash::make('password'),
            ]
        );

        $tksi = User::updateOrCreate(
            ['email' => 'tksi@sikes.test'],
            [
                'name' => 'Petugas TKSI',
                'role' => 'tksi',
                'password' => Hash::make('password'),
            ]
        );


        // =====================================================
        // JURUSAN
        // =====================================================

        $titl = Jurusan::updateOrCreate(
            ['kode' => 'TITL'],
            [
                'nama_jurusan' =>
                    'Teknik Instalasi Tenaga Listrik',
            ]
        );

        $tei = Jurusan::updateOrCreate(
            ['kode' => 'TEI'],
            [
                'nama_jurusan' =>
                    'Teknik Elektronika Industri',
            ]
        );

        $tkr = Jurusan::updateOrCreate(
            ['kode' => 'TKR'],
            [
                'nama_jurusan' =>
                    'Teknik Kendaraan Ringan',
            ]
        );

        $tkp = Jurusan::updateOrCreate(
            ['kode' => 'TKP'],
            [
                'nama_jurusan' =>
                    'Teknik Konstruksi dan Perumahan',
            ]
        );

        $tp = Jurusan::updateOrCreate(
            ['kode' => 'TP'],
            [
                'nama_jurusan' =>
                    'Teknik Pemesinan',
            ]
        );


        // =====================================================
        // KELAS
        // =====================================================

        $kelasData = [

            // X
            [
                'nama_kelas' => 'X TITL',
                'tingkat' => 10,
                'jurusan_id' => $titl->id,
            ],
            [
                'nama_kelas' => 'X TEI',
                'tingkat' => 10,
                'jurusan_id' => $tei->id,
            ],
            [
                'nama_kelas' => 'X TKR',
                'tingkat' => 10,
                'jurusan_id' => $tkr->id,
            ],
            [
                'nama_kelas' => 'X TKP',
                'tingkat' => 10,
                'jurusan_id' => $tkp->id,
            ],
            [
                'nama_kelas' => 'X TP',
                'tingkat' => 10,
                'jurusan_id' => $tp->id,
            ],

            // XI
            [
                'nama_kelas' => 'XI TITL',
                'tingkat' => 11,
                'jurusan_id' => $titl->id,
            ],
            [
                'nama_kelas' => 'XI TEI',
                'tingkat' => 11,
                'jurusan_id' => $tei->id,
            ],
            [
                'nama_kelas' => 'XI TKR',
                'tingkat' => 11,
                'jurusan_id' => $tkr->id,
            ],
            [
                'nama_kelas' => 'XI TKP',
                'tingkat' => 11,
                'jurusan_id' => $tkp->id,
            ],
            [
                'nama_kelas' => 'XI TP',
                'tingkat' => 11,
                'jurusan_id' => $tp->id,
            ],

            // XII
            [
                'nama_kelas' => 'XII TITL',
                'tingkat' => 12,
                'jurusan_id' => $titl->id,
            ],
            [
                'nama_kelas' => 'XII TEI',
                'tingkat' => 12,
                'jurusan_id' => $tei->id,
            ],
            [
                'nama_kelas' => 'XII TKR',
                'tingkat' => 12,
                'jurusan_id' => $tkr->id,
            ],
            [
                'nama_kelas' => 'XII TKP',
                'tingkat' => 12,
                'jurusan_id' => $tkp->id,
            ],
            [
                'nama_kelas' => 'XII TP',
                'tingkat' => 12,
                'jurusan_id' => $tp->id,
            ],
        ];


        $kelas = [];

        foreach ($kelasData as $data) {

            $kelas[] = Kelas::updateOrCreate(
                [
                    'nama_kelas' => $data['nama_kelas'],
                ],
                [
                    'tingkat' => $data['tingkat'],
                    'jurusan_id' => $data['jurusan_id'],
                ]
            );
        }


        // =====================================================
        // SISWA
        // =====================================================
        //
        // Membuat 30 siswa.
        // 10 siswa X
        // 10 siswa XI
        // 10 siswa XII
        //
        // =====================================================

        $namaSiswa = [

            'Ahmad Fauzan',
            'Budi Santoso',
            'Cahyo Pratama',
            'Dimas Saputra',
            'Eko Setiawan',
            'Fajar Nugroho',
            'Galih Ramadhan',
            'Hendra Wijaya',
            'Ilham Maulana',
            'Joko Susanto',

            'Andi Kurniawan',
            'Bagas Firmansyah',
            'Candra Wijaya',
            'Dani Ramadhan',
            'Erwin Saputra',
            'Farhan Akbar',
            'Gilang Pratama',
            'Hafiz Maulana',
            'Iqbal Ramadhan',
            'Jefri Setiawan',

            'Krisna Putra',
            'Lukman Hakim',
            'M. Rizky',
            'Nanda Pratama',
            'Oki Setiawan',
            'Putra Ramadhan',
            'Rian Saputra',
            'Satria Nugraha',
            'Taufik Hidayat',
            'Yoga Pratama',
        ];


        $siswas = [];

        foreach ($namaSiswa as $index => $nama) {

            /*
            |--------------------------------------------------
            | Tentukan kelas
            |--------------------------------------------------
            |
            | 0 - 9   = X
            | 10 - 19 = XI
            | 20 - 29 = XII
            |
            */

            if ($index < 10) {

                $tingkat = 10;

            } elseif ($index < 20) {

                $tingkat = 11;

            } else {

                $tingkat = 12;
            }


            /*
            |--------------------------------------------------
            | Ambil kelas
            |--------------------------------------------------
            */

            $kelasSiswa = collect($kelas)
                ->where('tingkat', $tingkat)
                ->values()
                ->get($index % 5);


            /*
            |--------------------------------------------------
            | NISN
            |--------------------------------------------------
            */

            $nisn = '0062026' . str_pad(
                $index + 1,
                4,
                '0',
                STR_PAD_LEFT
            );


            /*
            |--------------------------------------------------
            | Buat siswa
            |--------------------------------------------------
            */

            $siswa = Siswa::updateOrCreate(
                [
                    'nisn' => $nisn,
                ],
                [
                    'nama' => $nama,
                    'kelas_id' => $kelasSiswa->id,
                ]
            );


            $siswas[] = $siswa;
        }


        // =====================================================
        // PERIODE AKTIF
        // =====================================================
        //
        // FASE 2
        //
        // Berkala 1 = VIEW
        // Berkala 2 = OPEN
        //
        // =====================================================

        Periode::where('status', 'aktif')
            ->update([
                'status' => 'nonaktif',
            ]);


        $periode = Periode::updateOrCreate(
            [
                'nama_periode' => 'Periode 2026 Fase 2',
            ],
            [
                'tanggal_mulai' => '2026-04-01',
                'tanggal_selesai' => '2026-09-30',
                'status' => 'aktif',
            ]
        );


        // =====================================================
        // DAFTARKAN SEMUA SISWA KE PERIODE
        // =====================================================

        foreach ($siswas as $siswa) {

            $periode->siswa()->syncWithoutDetaching([
                $siswa->id,
            ]);
        }


        // =====================================================
        // PEMERIKSAAN BERKALA 1
        // =====================================================
        //
        // Semua siswa sudah menyelesaikan Berkala 1.
        //
        // Karena sekarang fase 2:
        //
        // Berkala 1
        // -> VIEW
        // -> bisa melihat hasil
        //
        // Berkala 2
        // -> OPEN
        // -> bisa mengisi
        //
        // =====================================================

        foreach ($siswas as $index => $siswa) {

            PemeriksaanBerkala::updateOrCreate(
                [
                    'periode_id' => $periode->id,
                    'siswa_id' => $siswa->id,
                    'jenis_pemeriksaan' => 'berkala_1',
                ],
                [

                    // =========================================
                    // TANGGAL
                    // =========================================

                    'tanggal_pemeriksaan' =>
                        '2026-05-' .
                        str_pad(
                            (($index % 20) + 1),
                            2,
                            '0',
                            STR_PAD_LEFT
                        ),


                    // =========================================
                    // ANTROPOMETRI
                    // =========================================

                    'berat_badan' =>
                        48 + ($index % 10) + 0.5,

                    'tinggi_badan' =>
                        158 + ($index % 10),

                    'imt' =>
                        19.20 + (($index % 5) * 0.15),


                    // =========================================
                    // TANDA VITAL
                    // =========================================

                    'tekanan_darah' => '110/70',

                    'denyut_nadi' =>
                        72 + ($index % 10),

                    'suhu_tubuh' =>
                        36.4 + (($index % 3) * 0.1),


                    // =========================================
                    // PEMERIKSAAN FISIK
                    // =========================================

                    'mata' => 'Normal',

                    'telinga' => 'Normal',

                    'gigi_mulut' => 'Baik',

                    'kondisi_umum' => 'Sehat',


                    // =========================================
                    // HASIL
                    // =========================================

                    'keluhan' =>
                        'Tidak ada keluhan',

                    'hasil_pemeriksaan' =>
                        'Kondisi kesehatan baik',

                    'rekomendasi' =>
                        'Pertahankan pola hidup sehat',


                    // =========================================
                    // STATUS
                    // =========================================

                    'status' => 'selesai',

                    'catatan' =>
                        'Pemeriksaan berkala 1 telah selesai.',


                    // =========================================
                    // PEMERIKSA
                    // =========================================

                    'pemeriksa_id' => $klinik->id,
                ]
            );
        }


        // =====================================================
        // SELESAI
        // =====================================================

        $this->command->info(
            'Seeder berhasil dibuat.'
        );

        $this->command->info(
            '30 siswa berhasil dibuat.'
        );

        $this->command->info(
            'Periode 2026 Fase 2 aktif.'
        );

        $this->command->info(
            'Berkala 1 = VIEW.'
        );

        $this->command->info(
            'Berkala 2 = OPEN.'
        );
    }
}