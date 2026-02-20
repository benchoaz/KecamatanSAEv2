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
            $table->string('hero_image_path')->nullable()->after('image_festival');
            $table->string('hero_image_alt')->nullable()->default('Pimpinan Daerah')->after('hero_image_path');
            $table->boolean('hero_image_active')->default(true)->after('hero_image_alt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            $table->dropColumn(['hero_image_path', 'hero_image_alt', 'hero_image_active']);
        });
    }
};
