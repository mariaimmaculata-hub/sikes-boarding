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
        Schema::create('pemeriksaan_berkala', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('jenis_pemeriksaan', [
                'berkala_1',
                'berkala_2'
            ]);

            $table->date('tanggal_pemeriksaan')->nullable();

            $table->enum('status', [
                'belum',
                'selesai'
            ])->default('belum');

            $table->text('hasil')->nullable();

            $table->text('catatan')->nullable();

            $table->foreignId('pemeriksa_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'periode_id',
                'siswa_id',
                'jenis_pemeriksaan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_berkala');
    }
};