<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObatBatch extends Model
{
    protected $table = 'obat_batches';

    protected $fillable = [
        'obat_id',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'jumlah',
        'stok',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
    ];

    /**
     * Obat induk
     */
    public function obat(): BelongsTo
    {
        return $this->belongsTo(
            Obat::class,
            'obat_id'
        );
    }

    /**
     * Riwayat penggunaan batch
     */
    public function kunjunganObat(): HasMany
    {
        return $this->hasMany(
            KunjunganObat::class,
            'obat_batch_id'
        );
    }
}