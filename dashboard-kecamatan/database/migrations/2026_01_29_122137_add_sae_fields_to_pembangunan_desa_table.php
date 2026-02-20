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
            $table->string('jenis_kegiatan')->nullable()->after('nama_kegiatan');
            $table->json('komponen_belanja')->nullable()->after('realisasi_anggaran');
        });
    }

    public function down(): void
    {
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropColumn(['jenis_kegiatan', 'komponen_belanja']);
        });
    }
};
