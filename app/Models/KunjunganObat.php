<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KunjunganObat extends Model
{
    protected $table = 'kunjungan_obat';

    protected $fillable = [
        'kunjungan_id',
        'obat_id',
        'jumlah',
        'keterangan',
    ];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(
            KunjunganKlinik::class,
            'kunjungan_id'
        );
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}