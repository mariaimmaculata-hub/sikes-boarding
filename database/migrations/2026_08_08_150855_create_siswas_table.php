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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            $table->string('nisn', 20)->unique();

            $table->string('nama');

            $table->string('tempat_lahir')->nullable();

            $table->date('tanggal_lahir')->nullable();

            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('angkatan')->nullable();

            $table->text('alamat')->nullable();

            $table->string('no_hp', 20)->nullable();

            $table->string('nama_orang_tua')->nullable();

            $table->string('no_hp_orang_tua', 20)->nullable();

            $table->enum('status', [
                'aktif',
                'lulus',
                'pindah',
                'nonaktif'
            ])->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};