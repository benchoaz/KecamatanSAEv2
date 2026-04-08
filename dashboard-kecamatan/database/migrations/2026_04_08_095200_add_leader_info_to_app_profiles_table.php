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
            $table->string('leader_name')->nullable()->after('hero_image_alt');
            $table->string('leader_title')->nullable()->after('leader_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            $table->dropColumn(['leader_name', 'leader_title']);
        });
    }
};
