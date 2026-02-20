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
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_complaint')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('office_hours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'phone',
                'whatsapp_complaint',
                'facebook_url',
                'instagram_url',
                'youtube_url',
                'office_hours'
            ]);
        });
    }
};
