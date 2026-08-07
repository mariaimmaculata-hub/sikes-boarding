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
        Schema::create('tksi_batches', function (Blueprint $table) {

    $table->id();

    $table->string('nama_tes');

    $table->date('tanggal');

    $table->string('periode');

    $table->json('kelas')->nullable();

    $table->json('jurusan')->nullable();

    $table->json('komponen')->nullable();

    $table->text('keterangan')->nullable();

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tksi_batches');
    }
};
