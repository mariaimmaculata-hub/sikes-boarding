<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->foreignId('penyakit_id')
                ->nullable()
                ->after('siswa_id')
                ->constrained('penyakits')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->dropForeign(['penyakit_id']);
            $table->dropColumn('penyakit_id');
        });
    }
};