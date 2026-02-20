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
        Schema::create('public_services', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('desa_id')->nullable();
            $table->string('nama_desa_manual')->nullable();
            $table->string('jenis_layanan');
            $table->text('uraian');
            $table->string('whatsapp');
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('Diterima Sistem');
            $table->string('ip_address')->nullable();
            $table->string('file_path_1')->nullable();
            $table->string('file_path_2')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_services');
    }
};
