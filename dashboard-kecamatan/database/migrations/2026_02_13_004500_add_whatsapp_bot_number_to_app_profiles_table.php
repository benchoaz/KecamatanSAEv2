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
        Schema::table('app_profiles', function (Blueprint $blueprint) {
            $blueprint->string('whatsapp_bot_number', 50)->nullable()->after('whatsapp_complaint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $blueprint) {
            $blueprint->dropColumn('whatsapp_bot_number');
        });
    }
};
