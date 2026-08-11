<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_berkala', function (Blueprint $table) {

            // ==========================================
            // ANTROPOMETRI
            // ==========================================

            $table->decimal('berat_badan', 5, 2)
                ->nullable()
                ->after('tanggal_pemeriksaan');

            $table->decimal('tinggi_badan', 5, 2)
                ->nullable()
                ->after('berat_badan');

            $table->decimal('imt', 5, 2)
                ->nullable()
                ->after('tinggi_badan');


            // ==========================================
            // TANDA VITAL
            // ==========================================

            $table->string('tekanan_darah', 20)
                ->nullable()
                ->after('imt');

            $table->unsignedSmallInteger('denyut_nadi')
                ->nullable()
                ->after('tekanan_darah');

            $table->decimal('suhu_tubuh', 4, 1)
                ->nullable()
                ->after('denyut_nadi');


            // ==========================================
            // PEMERIKSAAN FISIK
            // ==========================================

            $table->string('mata', 50)
                ->nullable()
                ->after('suhu_tubuh');

            $table->string('telinga', 50)
                ->nullable()
                ->after('mata');

            $table->string('gigi_mulut', 50)
                ->nullable()
                ->after('telinga');

            $table->string('kondisi_umum', 50)
                ->nullable()
                ->after('gigi_mulut');


            // ==========================================
            // HASIL PEMERIKSAAN
            // ==========================================

            $table->text('keluhan')
                ->nullable()
                ->after('kondisi_umum');

            $table->text('hasil_pemeriksaan')
                ->nullable()
                ->after('keluhan');

            $table->text('rekomendasi')
                ->nullable()
                ->after('hasil_pemeriksaan');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_berkala', function (Blueprint $table) {

            $table->dropColumn([
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
            ]);
        });
    }
};