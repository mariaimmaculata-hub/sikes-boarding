<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBerkala extends Model
{
    protected $table = 'pemeriksaan_berkala';
    
    protected $fillable = [
        'siswa_id',
        'periode_id',
        'tanggal_pemeriksaan',
        'jenis_pemeriksaan',

        'berat_badan',
        'tinggi_badan',
        'imt',

        'tekanan_darah',
        'denyut_nadi',
        'suhu_tubuh',

        'mata',
        'telinga',
        'gigi_mulut',
        'kondisi_umum',

        'keluhan',
        'hasil_pemeriksaan',
        'rekomendasi',
        'catatan',

        'status',
        'pemeriksa_id',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'imt' => 'decimal:2',
        'suhu_tubuh' => 'decimal:1',
    ];


    // ==========================================================
    // SISWA
    // ==========================================================

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }


    // ==========================================================
    // PERIODE
    // ==========================================================

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }


    // ==========================================================
    // PEMERIKSA
    // ==========================================================

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }
}