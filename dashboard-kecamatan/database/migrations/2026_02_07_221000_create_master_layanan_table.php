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
        Schema::create('master_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->text('deskripsi_syarat')->nullable();
            $table->string('estimasi_waktu')->nullable();
            $table->string('ikon')->default('fa-file-signature');
            $table->string('warna_bg')->default('bg-emerald-50');
            $table->string('warna_text')->default('text-emerald-600');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_layanan');
    }
};
