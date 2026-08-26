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
        Schema::create('obat_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('obat_id')
                ->constrained('obats')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('tanggal_masuk');

            $table->date('tanggal_kadaluarsa');

            /**
             * Jumlah awal ketika batch masuk
             */
            $table->unsignedInteger('jumlah')->default(0);

            /**
             * Sisa stok batch saat ini
             */
            $table->unsignedInteger('stok')->default(0);

            $table->timestamps();

            /**
             * Membantu pencarian batch berdasarkan
             * obat dan tanggal kadaluarsa.
             */
            $table->index([
                'obat_id',
                'tanggal_kadaluarsa',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_batches');
    }
};