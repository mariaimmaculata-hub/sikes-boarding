<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KunjunganKlinik extends Model
{
    protected $table = 'kunjungan_klinik';

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'penyakit_id',
        'tanggal_kunjungan',
        'keluhan',
        'pemeriksaan',
        'diagnosis',
        'tindakan',
        'status',
        'catatan',
        'pemeriksa_id',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    // ==========================================================
    // RELASI
    // ==========================================================

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(
            Periode::class,
            'periode_id'
        );
    }

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(
            Penyakit::class,
            'penyakit_id'
        );
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'pemeriksa_id'
        );
    }

    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(
            KunjunganObat::class,
            'kunjungan_id',
            'id'
        );
    }
}