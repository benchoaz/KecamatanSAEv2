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
        // A & B: Administrasi Perangkat & BPD
        Schema::create('personil_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa');
            $table->string('nama');
            $table->string('nik', 16);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jabatan'); // Kades, Sekdes, Ketua BPD, etc
            $table->string('kategori'); // perangkat, bpd
            $table->date('masa_jabatan_mulai')->nullable();
            $table->date('masa_jabatan_selesai')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('file_sk')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // C: Lembaga & Organisasi Kemasyarakatan
        Schema::create('lembaga_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa');
            $table->string('nama_lembaga');
            $table->string('tipe_lembaga'); // PKK, LPM, Karang Taruna, etc
            $table->string('ketua')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->string('file_sk')->nullable();
            $table->year('tahun_pembentukan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // D: Perencanaan Desa (Musrenbang)
        Schema::create('perencanaan_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa');
            $table->year('tahun');
            $table->date('tanggal_kegiatan');
            $table->string('lokasi');
            $table->string('file_ba')->nullable(); // Berita Acara
            $table->string('file_absensi')->nullable();
            $table->string('file_foto')->nullable();
            $table->string('status_administrasi')->default('draft');
            $table->text('catatan_kecamatan')->nullable();
            $table->timestamps();
        });

        Schema::create('usulan_musrenbang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perencanaan_id')->constrained('perencanaan_desa')->onDelete('cascade');
            $table->string('bidang');
            $table->text('uraian');
            $table->string('lokasi')->nullable();
            $table->string('pengusul')->nullable();
            $table->enum('prioritas', ['tinggi', 'sedang', 'rendah'])->default('sedang');
            $table->timestamps();
        });

        // F: Inventaris Desa (Aset)
        Schema::create('inventaris_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa');
            $table->enum('tipe_aset', ['barang', 'tanah']);
            $table->string('nama_item');
            $table->string('kode_item')->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->string('sumber_dana')->nullable();
            $table->string('kondisi')->nullable(); // Baik, Rusak Ringan, Rusak Berat
            $table->string('lokasi')->nullable();
            $table->string('luas')->nullable();
            $table->string('nomor_legalitas')->nullable(); // Sertifikat / No SK
            $table->string('peruntukan')->nullable();
            $table->string('status_sengketa')->default('aman'); // aman, sengketa, klaim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_desa');
        Schema::dropIfExists('usulan_musrenbang');
        Schema::dropIfExists('perencanaan_desa');
        Schema::dropIfExists('lembaga_desa');
        Schema::dropIfExists('personil_desa');
    }
};
