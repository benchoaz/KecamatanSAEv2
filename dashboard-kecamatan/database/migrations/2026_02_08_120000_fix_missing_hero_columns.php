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
        Schema::table('app_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('app_profiles', 'hero_bg_path')) {
                $table->string('hero_bg_path')->nullable()->comment('Path gambar background hero (pemandangan desa)');
            }
            if (!Schema::hasColumn('app_profiles', 'hero_bg_opacity')) {
                $table->tinyInteger('hero_bg_opacity')->default(10)->comment('Opacity background 1-100 (%)');
            }
            if (!Schema::hasColumn('app_profiles', 'hero_bg_blur')) {
                $table->tinyInteger('hero_bg_blur')->default(6)->comment('Blur intensity 0-10 (px)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            $table->dropColumn(['hero_bg_path', 'hero_bg_opacity', 'hero_bg_blur']);
        });
    }
};
