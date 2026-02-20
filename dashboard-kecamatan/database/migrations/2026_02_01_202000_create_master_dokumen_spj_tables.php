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
        // 1. Master Dokumen SPJ
        Schema::create('master_dokumen_spj', function (Blueprint $table) {
            $table->id();
            $table->string('kode_dokumen', 20)->unique();
            $table->string('nama_dokumen');
            $table->enum('kategori', ['umum', 'fisik', 'honor', 'perjalanan', 'surat']);
            $table->string('file_template')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Pivot: Kegiatan - Dokumen SPJ
        Schema::create('kegiatan_dokumen_spj', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_kegiatan_id')->index();
            $table->unsignedBigInteger('master_dokumen_spj_id')->index();
            $table->boolean('is_wajib')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('master_kegiatan_id')->references('id')->on('master_kegiatan')->onDelete('cascade');
            $table->foreign('master_dokumen_spj_id')->references('id')->on('master_dokumen_spj')->onDelete('cascade');

            $table->unique(['master_kegiatan_id', 'master_dokumen_spj_id'], 'kegiatan_dokumen_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_dokumen_spj');
        Schema::dropIfExists('master_dokumen_spj');
    }
};
