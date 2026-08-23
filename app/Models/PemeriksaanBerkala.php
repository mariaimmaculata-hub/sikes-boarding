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

    // Antropometri
    'berat_badan',
    'tinggi_badan',
    'imt',

    'mata',
    'telinga',
    'gigi_mulut',
    'kondisi_umum',

    // Tanda vital
    'tekanan_darah',
    'denyut_nadi',
    'suhu_tubuh',
    'saturasi_oksigen',

    // Kebersihan
    'kebersihan_rambut',
    'kebersihan_wajah',
    'kebersihan_telinga',
    'kebersihan_hidung',
    'kebersihan_mulut_gigi',
    'kebersihan_tangan_kuku',
    'kebersihan_kulit_badan',
    'kebersihan_kaki_kuku',

    // Hasil
    'keluhan',
    'hasil_pemeriksaan',
    'rekomendasi',

    // Status
    'status',
    'catatan',
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