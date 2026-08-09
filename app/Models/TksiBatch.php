<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TksiBatch extends Model
{
    protected $fillable = [
        'periode_id',
        'nama_tes',
        'tanggal',
        'kelas',
        'jurusan',
        'komponen',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kelas' => 'array',
        'jurusan' => 'array',
        'komponen' => 'array',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(
            TksiBatchSiswa::class,
            'tksi_batch_id'
        );
    }
}