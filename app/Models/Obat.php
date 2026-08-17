<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $fillable = [
        'nama_obat',
        'satuan',
        'stok',
        'keterangan',
    ];

    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(KunjunganObat::class);
    }
    public function kunjunganKlinik()
{
    return $this->hasMany(KunjunganKlinik::class);
}
}