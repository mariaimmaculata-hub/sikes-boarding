<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBerkala extends Model
{
    protected $table = 'pemeriksaan_berkala';

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'jenis_pemeriksaan',
        'tanggal_pemeriksaan',
        'status',
        'hasil',
        'catatan',
        'pemeriksa_id',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }
}