<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // User yang membuat periode
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Data penghubung periode dengan siswa
    public function periodeSiswa(): HasMany
    {
        return $this->hasMany(PeriodeSiswa::class);
    }

    // Siswa yang mengikuti periode
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(
            Siswa::class,
            'periode_siswa',
            'periode_id',
            'siswa_id'
        );
    }

    // Pemeriksaan berkala dalam periode
    public function pemeriksaanBerkala(): HasMany
    {
        return $this->hasMany(PemeriksaanBerkala::class);
    }

    // Batch TKSI dalam periode
    public function tksiBatches(): HasMany
    {
        return $this->hasMany(TksiBatch::class);
    }

    // Kunjungan klinik dalam periode
    public function kunjunganKlinik(): HasMany
    {
        return $this->hasMany(KunjunganKlinik::class);
    }
}