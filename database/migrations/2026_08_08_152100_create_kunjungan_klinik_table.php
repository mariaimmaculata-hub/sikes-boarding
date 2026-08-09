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
        Schema::create('kunjungan_klinik', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dateTime('tanggal_kunjungan');

            $table->text('keluhan')->nullable();

            $table->text('pemeriksaan')->nullable();

            $table->text('diagnosis')->nullable();

            $table->text('tindakan')->nullable();

            $table->enum('status', [
                'selesai',
                'rujuk'
            ])->default('selesai');

            $table->text('catatan')->nullable();

            $table->foreignId('pemeriksa_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_klinik');
    }
};