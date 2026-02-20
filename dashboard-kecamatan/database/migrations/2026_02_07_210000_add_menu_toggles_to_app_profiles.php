<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('app_profiles', function (Blueprint $row) {
            $row->boolean('is_menu_pengaduan_active')->default(true)->after('hero_image_active');
            $row->boolean('is_menu_umkm_active')->default(true)->after('is_menu_pengaduan_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $row) {
            $row->dropColumn(['is_menu_pengaduan_active', 'is_menu_umkm_active']);
        });
    }
};
