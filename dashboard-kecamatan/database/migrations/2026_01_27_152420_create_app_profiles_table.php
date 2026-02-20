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
        Schema::create('app_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->string('region_name');
            $table->enum('region_level', ['desa', 'kecamatan', 'kabupaten'])->default('kecamatan');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_profiles');
    }
};
