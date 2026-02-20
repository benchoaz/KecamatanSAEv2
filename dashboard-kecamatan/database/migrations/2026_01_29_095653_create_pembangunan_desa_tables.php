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
        // 1. Tabel Pembangunan Desa
        Schema::create('pembangunan_desa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id')->index();
            $table->string('nama_kegiatan');
            $table->string('lokasi');
            $table->integer('tahun_anggaran');
            $table->string('bidang_apbdes')->nullable();
            $table->string('sumber_dana');
            $table->string('status_kegiatan'); // Belum Dimulai, Sedang Berjalan, Selesai, Tertunda
            $table->string('progres_fisik'); // 0-25%, 26-50%, etc
            $table->decimal('pagu_anggaran', 15, 2)->default(0);
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
            $table->string('rab_file')->nullable();
            $table->string('gambar_rencana_file')->nullable();
            $table->string('status_laporan')->default('Draft'); // Draft, Dikirim, Dikembalikan, Dicatat
            $table->text('catatan_desa')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('cascade');
        });

        // 2. Tabel BLT Desa
        Schema::create('blt_desa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id')->index();
            $table->integer('tahun_anggaran');
            $table->integer('jumlah_kpm');
            $table->integer('kpm_terealisasi')->default(0);
            $table->decimal('total_dana_tersalurkan', 15, 2)->default(0);
            $table->string('status_penyaluran'); // Tepat Waktu, Bertahap, Tertunda
            $table->string('dokumen_ba')->nullable();
            $table->string('foto_penyaluran')->nullable();
            $table->string('status_laporan')->default('Draft'); // Draft, Dikirim, Dikembalikan, Dicatat
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blt_desa');
        Schema::dropIfExists('pembangunan_desa');
    }
};
