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
            $table->string('image_umkm')->nullable()->after('logo_path');
            $table->string('image_pariwisata')->nullable()->after('image_umkm');
            $table->string('image_festival')->nullable()->after('image_pariwisata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_profiles', function (Blueprint $table) {
            //
        });
    }
};
