<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TksiHasil extends Model
{
    protected $table = 'tksi_hasil';

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'tanggal',
        'komponen',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai' => 'decimal:2',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}