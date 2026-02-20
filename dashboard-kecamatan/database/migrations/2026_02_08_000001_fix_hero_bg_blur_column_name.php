<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix the column with leading space if it exists
        if (Schema::hasColumn('app_profiles', ' hero_bg_blur')) {
            Schema::table('app_profiles', function (Blueprint $table) {
                $table->renameColumn(' hero_bg_blur', 'hero_bg_blur');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('app_profiles', 'hero_bg_blur')) {
            Schema::table('app_profiles', function (Blueprint $table) {
                $table->renameColumn('hero_bg_blur', ' hero_bg_blur');
            });
        }
    }
};
