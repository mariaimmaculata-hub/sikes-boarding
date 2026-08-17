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

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function periodeSiswa(): HasMany
    {
        return $this->hasMany(PeriodeSiswa::class);
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(
            Siswa::class,
            'periode_siswa',
            'periode_id',
            'siswa_id'
        );
    }

    public function pemeriksaanBerkala(): HasMany
    {
        return $this->hasMany(PemeriksaanBerkala::class);
    }

    public function tksiBatches(): HasMany
    {
        return $this->hasMany(TksiBatch::class);
    }

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
     * Fase 1:
     * tanggal_mulai sampai 3 bulan pertama
     *
     * Fase 2:
     * setelah 3 bulan sampai tanggal_selesai
     */
    public function fasePemeriksaan(?Carbon $tanggal = null): int
    {
        $tanggal = $tanggal ?? now();

        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalBatasBerkala1 = $tanggalMulai->copy()->addMonths(3);

        if ($tanggal->lt($tanggalBatasBerkala1)) {
            return 1;
        }

        return 2;
    }


    /**
     * Apakah Berkala 1 sedang dibuka?
     */
    public function berkala1Terbuka(?Carbon $tanggal = null): bool
    {
        return $this->fasePemeriksaan($tanggal) === 1;
    }


    /**
     * Apakah Berkala 2 sedang dibuka?
     */
    public function berkala2Terbuka(?Carbon $tanggal = null): bool
    {
        return $this->fasePemeriksaan($tanggal) === 2;
    }


    /**
     * Status akses pemeriksaan.
     *
     * open   = bisa diisi
     * view   = hanya bisa dilihat
     * closed = belum waktunya
     */
    public function statusAksesPemeriksaan(
    string $jenis,
    ?Carbon $tanggal = null
): string {

    $tanggal = $tanggal ?? now();

    $tanggalMulai = Carbon::parse($this->tanggal_mulai);
    $tanggalSelesai = Carbon::parse($this->tanggal_selesai);

    // ======================================================
    // SETELAH PERIODE SELESAI
    // SEMUA HANYA BISA DILIHAT
    // ======================================================

    if ($tanggal->gt($tanggalSelesai)) {

        return 'view';

    }


    // ======================================================
    // FASE PEMERIKSAAN
    // ======================================================

    $fase = $this->fasePemeriksaan($tanggal);


    // ======================================================
    // BERKALA 1
    // ======================================================

    if ($jenis === 'berkala_1') {

        return $fase === 1
            ? 'open'
            : 'view';

    }


    // ======================================================
    // BERKALA 2
    // ======================================================

    if ($jenis === 'berkala_2') {

        return $fase === 2
            ? 'open'
            : 'closed';

    }


    return 'closed';
}
}