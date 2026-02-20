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
            $table->string('indikator_internal')->nullable()->after('status_laporan'); // Wajar, Perlu Klarifikasi
            $table->text('catatan_kecamatan')->nullable()->after('indikator_internal');
        });

        Schema::table('blt_desa', function (Blueprint $table) {
            $table->string('indikator_internal')->nullable()->after('status_laporan'); // Wajar, Perlu Klarifikasi
            $table->text('catatan_kecamatan')->nullable()->after('indikator_internal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropColumn(['indikator_internal', 'catatan_kecamatan']);
        });

        Schema::table('blt_desa', function (Blueprint $table) {
            $table->dropColumn(['indikator_internal', 'catatan_kecamatan']);
        });
    }
};
