<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Siswa extends Model
{
    protected $fillable = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'kelas_id',
        'angkatan',
        'alamat',
        'no_hp',
        'nama_orang_tua',
        'no_hp_orang_tua',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // =====================================================
    // KELAS
    // =====================================================

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    // =====================================================
    // PERIODE
    // =====================================================

    public function periodeSiswa(): HasMany
    {
        return $this->hasMany(PeriodeSiswa::class);
    }

    public function periode(): BelongsToMany
    {
        return $this->belongsToMany(
            Periode::class,
            'periode_siswa',
            'siswa_id',
            'periode_id'
        );
    }

    // =====================================================
    // PEMERIKSAAN BERKALA
    // =====================================================

    public function pemeriksaanBerkala(): HasMany
    {
        return $this->hasMany(PemeriksaanBerkala::class);
    }

    // =====================================================
    // KUNJUNGAN KLINIK
    // =====================================================

    public function kunjunganKlinik(): HasMany
    {
        return $this->hasMany(KunjunganKlinik::class);
    }

    // =====================================================
    // TKSI
    // =====================================================

    public function tksiPeserta(): HasMany
    {
        return $this->hasMany(
            TksiBatchSiswa::class,
            'siswa_id'
        );
    }
}