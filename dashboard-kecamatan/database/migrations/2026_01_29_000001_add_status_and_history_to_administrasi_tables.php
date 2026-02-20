<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Personil Desa
        Schema::table('personil_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('personil_desa', 'status'))
                $table->enum('status', ['draft', 'dikirim', 'dikembalikan', 'diterima'])->default('draft')->after('is_active');
            if (!Schema::hasColumn('personil_desa', 'catatan'))
                $table->text('catatan')->nullable()->after('status');
            if (!Schema::hasColumn('personil_desa', 'tanggal_pengajuan'))
                $table->timestamp('tanggal_pengajuan')->nullable()->after('catatan');
            if (!Schema::hasColumn('personil_desa', 'tanggal_verifikasi'))
                $table->timestamp('tanggal_verifikasi')->nullable()->after('tanggal_pengajuan');
        });

        // 2. Lembaga Desa
        Schema::table('lembaga_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('lembaga_desa', 'status'))
                $table->enum('status', ['draft', 'dikirim', 'dikembalikan', 'diterima'])->default('draft')->after('file_sk');
            if (!Schema::hasColumn('lembaga_desa', 'catatan'))
                $table->text('catatan')->nullable()->after('status');
            if (!Schema::hasColumn('lembaga_desa', 'tanggal_pengajuan'))
                $table->timestamp('tanggal_pengajuan')->nullable()->after('catatan');
            if (!Schema::hasColumn('lembaga_desa', 'tanggal_verifikasi'))
                $table->timestamp('tanggal_verifikasi')->nullable()->after('tanggal_pengajuan');
        });

        // 3. Dokumen Desa
        Schema::table('dokumen_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('dokumen_desa', 'status'))
                $table->enum('status', ['draft', 'dikirim', 'dikembalikan', 'diterima'])->default('draft')->after('file_path');
            if (!Schema::hasColumn('dokumen_desa', 'catatan'))
                $table->text('catatan')->nullable()->after('status');
            if (!Schema::hasColumn('dokumen_desa', 'tanggal_pengajuan'))
                $table->timestamp('tanggal_pengajuan')->nullable()->after('catatan');
            if (!Schema::hasColumn('dokumen_desa', 'tanggal_verifikasi'))
                $table->timestamp('tanggal_verifikasi')->nullable()->after('tanggal_pengajuan');
        });

        // 4. Riwayat Jabatan Personil
        Schema::create('riwayat_jabatan_personil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personil_desa_id')->constrained('personil_desa')->onDelete('cascade');
            $table->string('jabatan_lama')->nullable();
            $table->string('jabatan_baru');
            $table->date('tmt_lama')->nullable();
            $table->date('tmt_baru');
            $table->string('sk_lama')->nullable();
            $table->string('sk_baru')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatan_personil');

        Schema::table('personil_desa', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan', 'tanggal_pengajuan', 'tanggal_verifikasi']);
        });

        Schema::table('lembaga_desa', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan', 'tanggal_pengajuan', 'tanggal_verifikasi']);
        });

        Schema::table('dokumen_desa', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan', 'tanggal_pengajuan', 'tanggal_verifikasi']);
        });
    }
};
