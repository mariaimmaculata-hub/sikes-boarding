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
        Schema::table('tksi_hasil', function (Blueprint $table) {

            $table->unsignedInteger('level')
                ->nullable()
                ->after('nilai');

            $table->unsignedInteger('balikan')
                ->nullable()
                ->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tksi_hasil', function (Blueprint $table) {

            $table->dropColumn([
                'level',
                'balikan',
            ]);
        });
    }
};