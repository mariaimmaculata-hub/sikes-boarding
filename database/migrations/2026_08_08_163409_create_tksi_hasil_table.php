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

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->string('komponen', 50);

            $table->decimal('nilai', 10, 2);

            $table->text('catatan')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Satu siswa hanya boleh mempunyai satu hasil
            | untuk satu komponen dalam satu periode.
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['periode_id', 'siswa_id', 'komponen'],
                'tksi_hasil_periode_siswa_komponen_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tksi_hasil');
    }
};