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
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->string('sub_bidang')->nullable()->after('bidang_apbdes');
            $table->string('foto_sebelum_file')->nullable()->after('gambar_rencana_file');
            $table->string('foto_progres_file')->nullable()->after('foto_sebelum_file');
            $table->string('foto_selesai_file')->nullable()->after('foto_progres_file');
            $table->timestamp('foto_timestamp')->nullable()->after('foto_selesai_file');
        });

        Schema::table('blt_desa', function (Blueprint $table) {
            $table->string('dasar_penetapan')->nullable()->after('tahun_anggaran'); // Perdes / SK Kades
            $table->string('tahap_penyaluran')->nullable()->after('total_dana_tersalurkan');
            $table->string('daftar_kpm_file')->nullable()->after('dokumen_ba');
            $table->text('alasan_tertunda')->nullable()->after('status_penyaluran');
        });
    }

    public function down(): void
    {
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropColumn(['sub_bidang', 'foto_sebelum_file', 'foto_progres_file', 'foto_selesai_file', 'foto_timestamp']);
        });

        Schema::table('blt_desa', function (Blueprint $table) {
            $table->dropColumn(['dasar_penetapan', 'tahap_penyaluran', 'daftar_kpm_file', 'alasan_tertunda']);
        });
    }
};
