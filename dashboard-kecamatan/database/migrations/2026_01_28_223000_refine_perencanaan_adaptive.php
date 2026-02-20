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
        Schema::table('perencanaan_desa', function (Blueprint $table) {
            // Add adaptive planning columns
            $table->string('tipe_dokumen')->after('desa_id'); // RPJMDes, RKPDes, APBDes
            $table->string('mode_input')->after('tahun'); // arsip, transisi, terstruktur
            $table->string('nomor_perdes')->nullable()->after('mode_input');
            $table->date('tanggal_perdes')->nullable()->after('nomor_perdes');

            // Rename and standardize status
            if (Schema::hasColumn('perencanaan_desa', 'status_administrasi')) {
                $table->dropColumn('status_administrasi');
            }
            $table->string('status')->default('draft')->after('file_foto'); // draft, dikirim, dikembalikan, diterima

            // Hierarchy for terstruktur mode
            $table->unsignedBigInteger('parent_id')->nullable()->after('status');

            // Adjustments for archive mode
            $table->date('tanggal_kegiatan')->nullable()->change();
            $table->string('lokasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaan_desa', function (Blueprint $table) {
            $table->dropColumn(['tipe_dokumen', 'mode_input', 'nomor_perdes', 'tanggal_perdes', 'status', 'parent_id']);
            $table->string('status_administrasi')->default('draft');
        });
    }
};
