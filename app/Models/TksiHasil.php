<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TksiHasil extends Model
{
    protected $table = 'tksi_hasil';

    protected $fillable = [
        'siswa_id',
        'periode_id',
        'komponen',
        'tanggal',
        'nilai',
        'level',
        'balikan',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai' => 'float',
        'level' => 'integer',
        'balikan' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}