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
        Schema::table('kunjungan_obat', function (Blueprint $table) {
            $table->foreignId('obat_batch_id')
                ->nullable()
                ->after('obat_id')
                ->constrained('obat_batches')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan_obat', function (Blueprint $table) {
            $table->dropForeign([
                'obat_batch_id'
            ]);

            $table->dropColumn('obat_batch_id');
        });
    }
};