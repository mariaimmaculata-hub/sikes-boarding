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
        'obat_batch_id',
        'jumlah',
        'keterangan',
    ];

    /**
     * Kunjungan klinik
     */
    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(
            KunjunganKlinik::class,
            'kunjungan_id'
        );
    }

    /**
     * Obat
     */
    public function obat(): BelongsTo
    {
        return $this->belongsTo(
            Obat::class,
            'obat_id'
        );
    }

    /**
     * Batch obat yang digunakan
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            ObatBatch::class,
            'obat_batch_id'
        );
    }
}