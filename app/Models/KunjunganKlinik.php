<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KunjunganKlinik extends Model
{
    protected $table = 'kunjungan_klinik';

    /**
     * ============================================================
     * MASS ASSIGNMENT
     * ============================================================
     */
    protected $fillable = [
        'periode_id',
        'siswa_id',
        'tanggal_kunjungan',
        'keluhan',
        'pemeriksaan',
        'penyakit_id',
        'tindakan',
        'status',
        'catatan',
        'pemeriksa_id',
    ];

    /**
     * ============================================================
     * CAST
     * ============================================================
     *
     * Mengubah tanggal_kunjungan dari string database
     * menjadi object Carbon.
     */
    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    /**
     * ============================================================
     * RELASI SISWA
     * ============================================================
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * ============================================================
     * RELASI PERIODE
     * ============================================================
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    /**
     * ============================================================
     * RELASI PENYAKIT
     * ============================================================
     */
    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }

    /**
     * ============================================================
     * RELASI PEMERIKSA
     * ============================================================
     */
    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'pemeriksa_id'
        );
    }

    /**
     * ============================================================
     * RELASI OBAT
     * ============================================================
     */
    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(
            KunjunganObat::class,
            'kunjungan_id'
        );
    }
}