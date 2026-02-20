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
        Schema::create('dokumen_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa');
            $table->string('tipe_dokumen'); // LKPJ, LPPD, RPJMDes, RKPDes, APBDes
            $table->year('tahun');
            $table->date('tanggal_penyampaian')->nullable();
            $table->string('file_path');
            $table->string('status_verifikasi')->default('disampaikan'); // disampaikan, perbaikan, terverifikasi
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_desa');
    }
};
