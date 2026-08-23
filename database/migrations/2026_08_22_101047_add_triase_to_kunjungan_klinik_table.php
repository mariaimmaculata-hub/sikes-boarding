<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->enum('triase', [
                'merah',
                'kuning',
                'hijau',
                'hitam',
            ])
            ->nullable()
            ->after('penyakit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan_klinik', function (Blueprint $table) {
            $table->dropColumn('triase');
        });
    }
};