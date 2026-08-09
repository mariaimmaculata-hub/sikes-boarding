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

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }

    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(
            KunjunganObat::class,
            'kunjungan_id'
        );
    }
}