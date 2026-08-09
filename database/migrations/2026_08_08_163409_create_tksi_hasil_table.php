<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tksi_hasil', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tksi_batch_siswa_id')
                ->constrained('tksi_batch_siswa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('komponen');

            $table->decimal('nilai', 8, 2)->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique([
                'tksi_batch_siswa_id',
                'komponen',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tksi_hasil');
    }
};