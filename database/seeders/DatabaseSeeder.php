<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\PeriodeSiswa;
use App\Models\PemeriksaanBerkala;
use App\Models\Obat;
use App\Models\Penyakit;
use App\Models\KunjunganKlinik;
use App\Models\KunjunganObat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================
        // USER
        // =====================================================

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sikes.test',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $klinik = User::create([
            'name' => 'Petugas Klinik',
            'email' => 'klinik@sikes.test',
            'role' => 'klinik',
            'password' => 'password',
        ]);

        $tksi = User::create([
            'name' => 'Petugas TKSI',
            'email' => 'tksi@sikes.test',
            'role' => 'tksi',
            'password' => 'password',
        ]);


        // =====================================================
        // JURUSAN
        // =====================================================

        $tkj = Jurusan::create([
            'kode' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
        ]);

        $rpl = Jurusan::create([
            'kode' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);


        // =====================================================
        // KELAS
        // =====================================================

        $x_tkj = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat' => 10,
            'jurusan_id' => $tkj->id,
        ]);

        $xi_tkj = Kelas::create([
            'nama_kelas' => 'XI TKJ 1',
            'tingkat' => 11,
            'jurusan_id' => $tkj->id,
        ]);

        $x_rpl = Kelas::create([
            'nama_kelas' => 'X RPL 1',
            'tingkat' => 10,
            'jurusan_id' => $rpl->id,
        ]);


        // =====================================================
        // SISWA
        // =====================================================

        $siswa1 = Siswa::create([
            'nisn' => '0012345678',
            'nama' => 'Ahmad Fauzan',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-05-12',
            'jenis_kelamin' => 'L',
            'kelas_id' => $x_tkj->id,
            'angkatan' => 2026,
            'alamat' => 'Semarang',
            'no_hp' => '081234567890',
            'nama_orang_tua' => 'Budi Fauzan',
            'no_hp_orang_tua' => '081234567891',
            'status' => 'aktif',
        ]);

        $siswa2 = Siswa::create([
            'nisn' => '0012345679',
            'nama' => 'Siti Aisyah',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-08-20',
            'jenis_kelamin' => 'P',
            'kelas_id' => $x_tkj->id,
            'angkatan' => 2026,
            'alamat' => 'Semarang',
            'no_hp' => '081234567892',
            'nama_orang_tua' => 'Ahmad',
            'no_hp_orang_tua' => '081234567893',
            'status' => 'aktif',
        ]);

        $siswa3 = Siswa::create([
            'nisn' => '0012345680',
            'nama' => 'Rizky Pratama',
            'tempat_lahir' => 'Pemalang',
            'tanggal_lahir' => '2009-03-15',
            'jenis_kelamin' => 'L',
            'kelas_id' => $xi_tkj->id,
            'angkatan' => 2025,
            'alamat' => 'Pemalang',
            'no_hp' => '081234567894',
            'nama_orang_tua' => 'Agus Pratama',
            'no_hp_orang_tua' => '081234567895',
            'status' => 'aktif',
        ]);

        $siswa4 = Siswa::create([
            'nisn' => '0012345681',
            'nama' => 'Dinda Permata',
            'tempat_lahir' => 'Semarang',
            'tanggal_lahir' => '2010-11-10',
            'jenis_kelamin' => 'P',
            'kelas_id' => $x_rpl->id,
            'angkatan' => 2026,
            'alamat' => 'Semarang',
            'no_hp' => '081234567896',
            'nama_orang_tua' => 'Dedi Permata',
            'no_hp_orang_tua' => '081234567897',
            'status' => 'aktif',
        ]);


        // =====================================================
        // PERIODE
        // =====================================================

        $periode1 = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'aktif',
            'created_by' => $admin->id,
        ]);

        $periode2 = Periode::create([
            'nama_periode' => 'Semester Genap 2026/2027',
            'tanggal_mulai' => '2027-01-01',
            'tanggal_selesai' => '2027-06-30',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);


        // =====================================================
        // PERIODE SISWA
        // =====================================================

        // Periode 1

        PeriodeSiswa::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa1->id,
        ]);

        PeriodeSiswa::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa2->id,
        ]);

        PeriodeSiswa::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa3->id,
        ]);

        PeriodeSiswa::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa4->id,
        ]);


        // Periode 2

        PeriodeSiswa::create([
            'periode_id' => $periode2->id,
            'siswa_id' => $siswa1->id,
        ]);

        PeriodeSiswa::create([
            'periode_id' => $periode2->id,
            'siswa_id' => $siswa2->id,
        ]);


        // =====================================================
        // PEMERIKSAAN BERKALA
        // PERIODE 1
        // =====================================================

        // -----------------------------------------------------
        // Ahmad - Berkala 1
        // -----------------------------------------------------

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa1->id,
            'jenis_pemeriksaan' => 'berkala_1',
            'tanggal_pemeriksaan' => '2026-08-05',
            'status' => 'selesai',
            'hasil' => 'Kondisi kesehatan baik.',
            'catatan' => 'Tidak ditemukan keluhan.',
            'pemeriksa_id' => $klinik->id,
        ]);

        // Ahmad - Berkala 2

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa1->id,
            'jenis_pemeriksaan' => 'berkala_2',
            'tanggal_pemeriksaan' => null,
            'status' => 'belum',
            'hasil' => null,
            'catatan' => null,
            'pemeriksa_id' => null,
        ]);


        // -----------------------------------------------------
        // Siti - Berkala 1
        // -----------------------------------------------------

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa2->id,
            'jenis_pemeriksaan' => 'berkala_1',
            'tanggal_pemeriksaan' => '2026-08-06',
            'status' => 'selesai',
            'hasil' => 'Kondisi kesehatan baik.',
            'catatan' => 'Tidak ditemukan keluhan.',
            'pemeriksa_id' => $klinik->id,
        ]);

        // Siti - Berkala 2

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa2->id,
            'jenis_pemeriksaan' => 'berkala_2',
            'tanggal_pemeriksaan' => null,
            'status' => 'belum',
            'hasil' => null,
            'catatan' => null,
            'pemeriksa_id' => null,
        ]);


        // -----------------------------------------------------
        // Rizky - Berkala 1
        // -----------------------------------------------------

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa3->id,
            'jenis_pemeriksaan' => 'berkala_1',
            'tanggal_pemeriksaan' => '2026-08-05',
            'status' => 'selesai',
            'hasil' => 'Kondisi kesehatan baik.',
            'catatan' => 'Berat badan perlu dipantau.',
            'pemeriksa_id' => $klinik->id,
        ]);

        // Rizky - Berkala 2

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa3->id,
            'jenis_pemeriksaan' => 'berkala_2',
            'tanggal_pemeriksaan' => null,
            'status' => 'belum',
            'hasil' => null,
            'catatan' => null,
            'pemeriksa_id' => null,
        ]);


        // -----------------------------------------------------
        // Dinda - Berkala 1
        // -----------------------------------------------------

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa4->id,
            'jenis_pemeriksaan' => 'berkala_1',
            'tanggal_pemeriksaan' => '2026-08-07',
            'status' => 'selesai',
            'hasil' => 'Kondisi kesehatan baik.',
            'catatan' => 'Tidak ditemukan keluhan.',
            'pemeriksa_id' => $klinik->id,
        ]);

        // Dinda - Berkala 2

        PemeriksaanBerkala::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa4->id,
            'jenis_pemeriksaan' => 'berkala_2',
            'tanggal_pemeriksaan' => null,
            'status' => 'belum',
            'hasil' => null,
            'catatan' => null,
            'pemeriksa_id' => null,
        ]);


        // =====================================================
        // OBAT
        // =====================================================

        $paracetamol = Obat::create([
            'nama_obat' => 'Paracetamol',
            'satuan' => 'Tablet',
            'stok' => 100,
            'keterangan' => 'Untuk membantu meredakan demam dan nyeri.',
        ]);

        $amoxicillin = Obat::create([
            'nama_obat' => 'Amoxicillin',
            'satuan' => 'Kapsul',
            'stok' => 50,
            'keterangan' => 'Antibiotik sesuai resep atau pemeriksaan petugas medis.',
        ]);

        $betadine = Obat::create([
            'nama_obat' => 'Betadine',
            'satuan' => 'Botol',
            'stok' => 20,
            'keterangan' => 'Antiseptik untuk membantu membersihkan luka.',
        ]);

        $minyakKayuPutih = Obat::create([
            'nama_obat' => 'Minyak Kayu Putih',
            'satuan' => 'Botol',
            'stok' => 30,
            'keterangan' => 'Digunakan sesuai kebutuhan.',
        ]);


        // =====================================================
        // KUNJUNGAN KLINIK
        // =====================================================

        $kunjungan1 = KunjunganKlinik::create([
            'periode_id' => $periode1->id,
            'siswa_id' => $siswa1->id,
            'tanggal_kunjungan' => '2026-08-08 09:30:00',
            'keluhan' => 'Demam dan sakit kepala.',
            'pemeriksaan' => 'Suhu tubuh 38,2°C. Siswa terlihat lemas.',
            'diagnosis' => 'Demam.',
            'tindakan' => 'Pemeriksaan dan pemberian obat.',
            'status' => 'selesai',
            'catatan' => 'Dianjurkan istirahat dan minum air yang cukup.',
            'pemeriksa_id' => $klinik->id,
        ]);


        // =====================================================
        // OBAT KUNJUNGAN
        // =====================================================

        KunjunganObat::create([
            'kunjungan_id' => $kunjungan1->id,
            'obat_id' => $paracetamol->id,
            'jumlah' => 2,
            'keterangan' => 'Diminum setelah makan.',
        ]);

        KunjunganObat::create([
            'kunjungan_id' => $kunjungan1->id,
            'obat_id' => $minyakKayuPutih->id,
            'jumlah' => 1,
            'keterangan' => 'Digunakan secukupnya.',
        ]);


        // =====================================================
        // PENYAKIT
        // =====================================================

        Penyakit::create([
            'nama_penyakit' => 'Demam',
            'kategori' => 'Umum',
            'keterangan' => 'Kondisi dengan peningkatan suhu tubuh.',
        ]);

        Penyakit::create([
            'nama_penyakit' => 'Batuk',
            'kategori' => 'Pernapasan',
            'keterangan' => 'Keluhan berupa batuk yang dapat disertai gejala lain.',
        ]);

        Penyakit::create([
            'nama_penyakit' => 'Flu',
            'kategori' => 'Pernapasan',
            'keterangan' => 'Keluhan yang dapat berupa pilek, batuk, dan demam.',
        ]);

        Penyakit::create([
            'nama_penyakit' => 'Sakit Kepala',
            'kategori' => 'Umum',
            'keterangan' => 'Keluhan berupa rasa sakit atau tidak nyaman pada kepala.',
        ]);

        Penyakit::create([
            'nama_penyakit' => 'Sakit Perut',
            'kategori' => 'Pencernaan',
            'keterangan' => 'Keluhan berupa rasa sakit atau tidak nyaman pada perut.',
        ]);
    }
}