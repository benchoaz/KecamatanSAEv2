<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lokers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('job_category'); // misal: Tukang Pijet, Buruh Tani, dll
            $table->unsignedBigInteger('desa_id')->nullable();
            $table->string('nama_desa_manual')->nullable();
            $table->string('contact_wa');
            $table->text('description')->nullable();
            $table->string('work_time')->nullable(); // harian, mingguan, dll
            $table->boolean('is_available_today')->default(false);
            $table->string('status')->default('menunggu_verifikasi'); // menunggu_verifikasi, aktif, nonaktif
            $table->boolean('is_sensitive')->default(false);
            $table->string('manage_token')->nullable();
            $table->string('source')->default('web_form'); // web_form, admin_input, whatsapp
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokers');
    }
};
