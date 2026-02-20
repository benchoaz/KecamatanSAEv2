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
        Schema::table('dokumen_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('dokumen_desa', 'nomor_dokumen')) {
                $table->string('nomor_dokumen')->nullable()->after('tipe_dokumen');
            }
            if (!Schema::hasColumn('dokumen_desa', 'perihal')) {
                $table->text('perihal')->nullable()->after('nomor_dokumen');
            }

            // Adjust tipe_dokumen to include Perdes
            // Note: Since this is string, we don't need to change type, but we track the standard
            // Standard: Perdes, LKPJ, LPPD, APBDes, Perkades, SK_Desa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_desa', function (Blueprint $table) {
            $table->dropColumn(['nomor_dokumen', 'perihal']);
        });
    }
};
