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
        Schema::create('work_directory', function (Blueprint $table) {
            $table->id();
            $table->string('display_name'); // Nama panggilan / inisial
            $table->string('job_category'); // Kategori: Jasa Harian, Transportasi, Keliling
            $table->enum('job_type', ['harian', 'jasa', 'keliling', 'transportasi']);
            $table->string('job_title'); // Tukang Sayur, Ojek, Tukang Bangunan, dll
            $table->string('service_area')->nullable(); // Desa / wilayah
            $table->string('service_time')->nullable(); // Jam layanan (opsional)
            $table->string('contact_phone'); // Telepon / WhatsApp
            $table->text('short_description')->nullable(); // Keterangan singkat
            $table->enum('data_source', ['kecamatan', 'desa', 'warga'])->default('kecamatan');
            $table->boolean('consent_public')->default(true); // Izin tampil publik
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_directory');
    }
};
