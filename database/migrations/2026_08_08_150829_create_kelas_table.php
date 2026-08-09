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
    Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kelas');
        $table->unsignedTinyInteger('tingkat');
        $table->foreignId('jurusan_id')
            ->constrained('jurusans')
            ->cascadeOnUpdate()
            ->restrictOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('kelas');
}
};
