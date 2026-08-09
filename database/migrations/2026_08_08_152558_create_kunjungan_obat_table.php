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
        Schema::create('kunjungan_obat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kunjungan_id')
                ->constrained('kunjungan_klinik')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('obat_id')
                ->constrained('obats')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('jumlah')->default(1);

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique([
                'kunjungan_id',
                'obat_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_obat');
    }
};