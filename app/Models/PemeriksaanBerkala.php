<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBerkala extends Model
{
    protected $table = 'pemeriksaan_berkala';

    protected $fillable = [

        // RELASI
        'periode_id',
        'siswa_id',
        'pemeriksa_id',

        // IDENTITAS PEMERIKSAAN
        'jenis_pemeriksaan',
        'tanggal_pemeriksaan',

        // ANTROPOMETRI
        'berat_badan',
        'tinggi_badan',
        'imt',

        // TANDA VITAL
        'tekanan_darah',
        'denyut_nadi',
        'suhu_tubuh',

        // PEMERIKSAAN FISIK
        'mata',
        'telinga',
        'gigi_mulut',
        'kondisi_umum',

        // HASIL
        'keluhan',
        'hasil_pemeriksaan',
        'rekomendasi',

        // STATUS
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',

        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'imt' => 'decimal:2',

        'suhu_tubuh' => 'decimal:1',

        'denyut_nadi' => 'integer',
    ];


    // ==========================================
    // RELASI PERIODE
    // ==========================================

    public function periode(): BelongsTo
    {
        return $this->belongsTo(
            Periode::class,
            'periode_id'
        );
    }


    // ==========================================
    // RELASI SISWA
    // ==========================================

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }


    // ==========================================
    // RELASI PEMERIKSA
    // ==========================================

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'pemeriksa_id'
        );
    }
}