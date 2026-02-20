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
            $table->date('tanggal_kegiatan')->nullable()->after('status_kegiatan');
            $table->integer('jumlah_peserta')->nullable()->after('tanggal_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kegiatan', 'jumlah_peserta']);
        });
    }
};
