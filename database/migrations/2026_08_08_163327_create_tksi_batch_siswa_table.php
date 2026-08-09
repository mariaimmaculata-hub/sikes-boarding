<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tksi_batch_siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tksi_batch_id')
                ->constrained('tksi_batches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', [
                'belum',
                'selesai',
            ])->default('belum');

            $table->timestamps();

            $table->unique([
                'tksi_batch_id',
                'siswa_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tksi_batch_siswa');
    }
};