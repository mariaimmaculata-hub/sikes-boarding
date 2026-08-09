<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodeSiswa extends Model
{
    protected $table = 'periode_siswa';

    protected $fillable = [
        'periode_id',
        'siswa_id',
    ];

    // Periode yang diikuti siswa
    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    // Siswa yang mengikuti periode
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}