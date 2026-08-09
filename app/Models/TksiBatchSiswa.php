<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TksiBatchSiswa extends Model
{
    protected $table = 'tksi_batch_siswa';

    protected $fillable = [
        'tksi_batch_id',
        'siswa_id',
        'status',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(TksiBatch::class, 'tksi_batch_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function hasil(): HasMany
    {
        return $this->hasMany(TksiHasil::class, 'tksi_batch_siswa_id');
    }
}