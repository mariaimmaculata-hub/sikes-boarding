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
    Schema::create('jurusans', function (Blueprint $table) {
        $table->id();
        $table->string('kode', 20)->unique();
        $table->string('nama_jurusan');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('jurusans');
}
};
