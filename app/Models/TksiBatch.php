<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TksiBatch extends Model
{

    protected $fillable = [

        'nama_tes',
        'tanggal',
        'periode',
        'kelas',
        'jurusan',
        'komponen',
        'keterangan'

    ];


    protected $casts = [

        'kelas'=>'array',
        'jurusan'=>'array',
        'komponen'=>'array'

    ];

}