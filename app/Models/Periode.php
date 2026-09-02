<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Periode extends Model
{
    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ==========================================================
    // RELASI
    // ==========================================================

    /**
     * User yang membuat periode.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Data siswa yang terdaftar pada periode.
     */
    public function periodeSiswa(): HasMany
    {
        return $this->hasMany(PeriodeSiswa::class);
    }

    /**
     * Relasi many-to-many periode dengan siswa.
     */
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(
            Siswa::class,
            'periode_siswa',
            'periode_id',
            'siswa_id'
        );
    }

    /**
     * Pemeriksaan berkala dalam periode.
     */
    public function pemeriksaanBerkala(): HasMany
    {
        return $this->hasMany(PemeriksaanBerkala::class);
    }

    /**
     * Batch TKSI dalam periode.
     */
    public function tksiBatches(): HasMany
    {
        return $this->hasMany(TksiBatch::class);
    }

    /**
     * Peserta TKSI melalui batch.
     */
    public function tksiPeserta(): HasManyThrough
    {
        return $this->hasManyThrough(
            TksiBatchSiswa::class,
            TksiBatch::class,
            'periode_id',
            'tksi_batch_id',
            'id',
            'id'
        );
    }

    /**
     * Kunjungan klinik dalam periode.
     */
    public function kunjunganKlinik(): HasMany
    {
        return $this->hasMany(KunjunganKlinik::class);
    }

    // ==========================================================
    // FASE PEMERIKSAAN BERKALA
    // ==========================================================

    /**
     * Menentukan fase pemeriksaan berdasarkan tanggal.
     *
     * Return:
     *
     * 0 = Di luar periode
     * 1 = Berkala 1
     * 2 = Berkala 2
     *
     * Contoh:
     *
     * Periode:
     * 1 Januari 2026 - 31 Desember 2026
     *
     * Berkala 1:
     * 1 Januari 2026 - 31 Maret 2026
     *
     * Berkala 2:
     * 1 April 2026 - 31 Desember 2026
     *
     * Catatan:
     * Setelah periode selesai, fase menjadi 0.
     * Namun status akses pemeriksaan TIDAK otomatis ditutup.
     * Status akses diatur oleh statusAksesPemeriksaan().
     */
    public function fasePemeriksaan(?Carbon $tanggal = null): int
    {
        $tanggal = ($tanggal ?? now())
            ->copy()
            ->startOfDay();

        $tanggalMulai = $this->tanggalMulaiBerkala1();
        $tanggalSelesai = $this->tanggalAkhirBerkala2();

        // ======================================================
        // SEBELUM PERIODE
        // ======================================================

        if ($tanggal->lt($tanggalMulai)) {
            return 0;
        }

        // ======================================================
        // SETELAH PERIODE
        // ======================================================

        if ($tanggal->gt($tanggalSelesai)) {
            return 0;
        }

        // ======================================================
        // AWAL BERKALA 2
        // ======================================================

        $tanggalMulaiBerkala2 = $this->tanggalMulaiBerkala2();

        // ======================================================
        // BERKALA 1
        // ======================================================

        if ($tanggal->lt($tanggalMulaiBerkala2)) {
            return 1;
        }

        // ======================================================
        // BERKALA 2
        // ======================================================

        return 2;
    }

    // ==========================================================
    // TANGGAL BERKALA 1
    // ==========================================================

    /**
     * Tanggal mulai Berkala 1.
     *
     * SAMA PERSIS dengan tanggal mulai periode.
     */
    public function tanggalMulaiBerkala1(): Carbon
    {
        return $this->tanggal_mulai
            ->copy()
            ->startOfDay();
    }

    /**
     * Tanggal akhir Berkala 1.
     *
     * Berkala 1 berlangsung selama 3 bulan
     * sejak tanggal mulai periode.
     *
     * Contoh:
     *
     * 1 Januari 2026 - 31 Maret 2026
     */
    public function tanggalAkhirBerkala1(): Carbon
    {
        $tanggalMulai = $this->tanggalMulaiBerkala1();
        $tanggalSelesai = $this->tanggalAkhirBerkala2();

        $tanggalAkhir = $tanggalMulai
            ->copy()
            ->addMonthsNoOverflow(3)
            ->subDay()
            ->startOfDay();

        // Jangan melewati tanggal selesai periode.
        if ($tanggalAkhir->gt($tanggalSelesai)) {
            return $tanggalSelesai->copy();
        }

        return $tanggalAkhir;
    }

    // ==========================================================
    // TANGGAL BERKALA 2
    // ==========================================================

    /**
     * Tanggal mulai Berkala 2.
     *
     * Tepat 3 bulan setelah tanggal mulai periode.
     *
     * Contoh:
     *
     * Berkala 1:
     * 1 Januari - 31 Maret 2026
     *
     * Berkala 2:
     * 1 April - 31 Desember 2026
     */
    public function tanggalMulaiBerkala2(): Carbon
    {
        return $this->tanggalMulaiBerkala1()
            ->copy()
            ->addMonthsNoOverflow(3)
            ->startOfDay();
    }

    /**
     * Tanggal akhir Berkala 2.
     *
     * SAMA PERSIS dengan tanggal selesai periode.
     */
    public function tanggalAkhirBerkala2(): Carbon
    {
        return $this->tanggal_selesai
            ->copy()
            ->startOfDay();
    }

    // ==========================================================
    // STATUS FASE
    // ==========================================================

    /**
     * Mengecek apakah saat ini sedang berada di fase Berkala 1.
     *
     * Hanya menunjukkan fase berdasarkan tanggal.
     */
    public function berkala1Terbuka(?Carbon $tanggal = null): bool
    {
        return $this->fasePemeriksaan($tanggal) === 1;
    }

    /**
     * Mengecek apakah saat ini sedang berada di fase Berkala 2.
     *
     * Hanya menunjukkan fase berdasarkan tanggal.
     */
    public function berkala2Terbuka(?Carbon $tanggal = null): bool
    {
        return $this->fasePemeriksaan($tanggal) === 2;
    }

    // ==========================================================
    // STATUS AKSES PEMERIKSAAN
    // ==========================================================

    /**
     * Menentukan status akses pemeriksaan.
     *
     * Return:
     *
     * open   = dapat diisi / diedit
     * view   = hanya dapat dilihat
     * closed = belum waktunya
     *
     * ==========================================================
     * ATURAN AKSES
     * ==========================================================
     *
     * SEBELUM PERIODE:
     *
     * Berkala 1 = closed
     * Berkala 2 = closed
     *
     * ==========================================================
     * SAAT BERKALA 1:
     *
     * Berkala 1 = open
     * Berkala 2 = closed
     *
     * ==========================================================
     * SAAT BERKALA 2:
     *
     * Berkala 1 = open
     * Berkala 2 = open
     *
     * ==========================================================
     * SETELAH PERIODE:
     *
     * Berkala 1 = open
     * Berkala 2 = open
     *
     * ==========================================================
     *
     * Jadi setelah suatu berkala sudah waktunya dibuka,
     * berkala tersebut TIDAK akan otomatis ditutup lagi.
     */
    public function statusAksesPemeriksaan(
        string $jenis,
        ?Carbon $tanggal = null
    ): string {
        $tanggal = ($tanggal ?? now())
            ->copy()
            ->startOfDay();

        $tanggalMulai = $this->tanggalMulaiBerkala1();
        $tanggalMulaiBerkala2 = $this->tanggalMulaiBerkala2();

        // ======================================================
        // SEBELUM PERIODE DIMULAI
        // ======================================================

        if ($tanggal->lt($tanggalMulai)) {
            return 'closed';
        }

        // ======================================================
        // BERKALA 1
        // ======================================================
        //
        // Begitu periode dimulai, Berkala 1 langsung OPEN.
        //
        // Status ini tetap OPEN:
        //
        // - Saat Berkala 1 aktif
        // - Saat Berkala 2 aktif
        // - Setelah periode selesai
        //
        // ======================================================

        if ($jenis === 'berkala_1') {
            return 'open';
        }

        // ======================================================
        // BERKALA 2
        // ======================================================
        //
        // Berkala 2 baru OPEN ketika tanggal sudah mencapai
        // tanggal mulai Berkala 2.
        //
        // Setelah itu tetap OPEN meskipun periode selesai.
        //
        // ======================================================

        if ($jenis === 'berkala_2') {

            if ($tanggal->lt($tanggalMulaiBerkala2)) {
                return 'closed';
            }

            return 'open';
        }

        // ======================================================
        // JENIS TIDAK DIKENAL
        // ======================================================

        return 'closed';
    }
}
