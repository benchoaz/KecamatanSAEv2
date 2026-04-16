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
        Schema::table('app_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('app_profiles', 'is_menu_berita_active')) {
                $table->boolean('is_menu_berita_active')->default(true)->after('is_menu_umkm_active');
            }
            if (!Schema::hasColumn('app_profiles', 'is_menu_pelayanan_active')) {
                $table->boolean('is_menu_pelayanan_active')->default(true)->after('is_menu_berita_active');
            }
            if (!Schema::hasColumn('app_profiles', 'is_menu_statistik_active')) {
                $table->boolean('is_menu_statistik_active')->default(true)->after('is_menu_pelayanan_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'is_menu_berita_active',
                'is_menu_pelayanan_active',
                'is_menu_statistik_active'
            ]);
        });
    }
};
