<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TksiHasil extends Model
{
    protected $table = 'tksi_hasil';

    protected $fillable = [
        'tksi_batch_siswa_id',
        'komponen',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function batchSiswa(): BelongsTo
    {
        return $this->belongsTo(
            TksiBatchSiswa::class,
            'tksi_batch_siswa_id'
        );
    }
}