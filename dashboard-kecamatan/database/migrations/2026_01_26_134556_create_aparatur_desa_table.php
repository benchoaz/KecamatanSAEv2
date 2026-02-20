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
        Schema::create('aparatur_desa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('desa_id')->constrained('desa')->onDelete('cascade');
            $table->string('nama_lengkap', 150);
            $table->char('nik', 16)->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('pendidikan_terakhir', 50)->nullable();

            // Jabatan Details
            $table->string('jabatan', 50)->comment('Kades, Sekdes, Kaur, Kasi, Kadus');
            $table->string('nomor_sk', 100);
            $table->date('tanggal_sk');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable(); // Essential for Kades, often open for Perangkat

            // Status & Verification
            $table->string('status_jabatan', 20)->default('Aktif')->comment('Aktif, Pj, Berakhir, Berhenti');
            $table->string('status_verifikasi', 30)->default('Belum Diverifikasi');
            $table->text('catatan_kecamatan')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aparatur_desa');
    }
};
