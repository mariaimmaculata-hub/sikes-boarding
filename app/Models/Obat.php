<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $fillable = [
        'nama_obat',
        'satuan',
        'keterangan',
    ];

    /**
     * Semua batch obat
     */
    public function batches(): HasMany
    {
        return $this->hasMany(
            ObatBatch::class,
            'obat_id'
        );
    }

    /**
     * Obat yang digunakan dalam kunjungan
     */
    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(
            KunjunganObat::class,
            'obat_id'
        );
    }

    /**
     * Kunjungan klinik yang menggunakan obat
     */
    public function kunjunganKlinik()
    {
        return $this->hasMany(
            KunjunganKlinik::class,
            'obat_id'
        );
    }
}