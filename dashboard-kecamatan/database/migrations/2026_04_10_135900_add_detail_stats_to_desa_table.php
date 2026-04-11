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
        Schema::table('desa', function (Blueprint $table) {
            $table->json('stat_pendidikan')->nullable();
            $table->json('stat_pekerjaan')->nullable();
            $table->json('stat_agama')->nullable();
            $table->json('stat_kesehatan')->nullable();
            $table->json('stat_desil')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropColumn([
                'stat_pendidikan',
                'stat_pekerjaan',
                'stat_agama',
                'stat_kesehatan',
                'stat_desil',
            ]);
        });
    }
};
