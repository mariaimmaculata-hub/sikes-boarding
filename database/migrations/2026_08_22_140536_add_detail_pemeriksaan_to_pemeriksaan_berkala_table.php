<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_berkala', function (Blueprint $table) {

            // =====================================================
            // TANDA VITAL
            // =====================================================

            $table->decimal('saturasi_oksigen', 5, 2)
                ->nullable()
                ->after('suhu_tubuh');

            // =====================================================
            // KEBERSIHAN DIRI
            // =====================================================

            $table->string('kebersihan_rambut', 30)
                ->nullable()
                ->after('saturasi_oksigen');

            $table->string('kebersihan_wajah', 30)
                ->nullable()
                ->after('kebersihan_rambut');

            $table->string('kebersihan_telinga', 30)
                ->nullable()
                ->after('kebersihan_wajah');

            $table->string('kebersihan_hidung', 30)
                ->nullable()
                ->after('kebersihan_telinga');

            $table->string('kebersihan_mulut_gigi', 30)
                ->nullable()
                ->after('kebersihan_hidung');

            $table->string('kebersihan_tangan_kuku', 30)
                ->nullable()
                ->after('kebersihan_mulut_gigi');

            $table->string('kebersihan_kulit_badan', 30)
                ->nullable()
                ->after('kebersihan_tangan_kuku');

            $table->string('kebersihan_kaki_kuku', 30)
                ->nullable()
                ->after('kebersihan_kulit_badan');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_berkala', function (Blueprint $table) {

            $table->dropColumn([
                'saturasi_oksigen',
                'kebersihan_rambut',
                'kebersihan_wajah',
                'kebersihan_telinga',
                'kebersihan_hidung',
                'kebersihan_mulut_gigi',
                'kebersihan_tangan_kuku',
                'kebersihan_kulit_badan',
                'kebersihan_kaki_kuku',
            ]);
        });
    }
};