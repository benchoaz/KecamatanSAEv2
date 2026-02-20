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
        Schema::create('pengunjung_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 16)->nullable();
            $table->foreignId('desa_asal_id')->nullable()->constrained('desa');
            $table->string('alamat_luar')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('tujuan_bidang'); // e.g., Pemerintahan, Ekbang, Camat, Sekcam
            $table->text('keperluan');
            $table->timestamp('jam_datang')->useCurrent();
            $table->enum('status', ['menunggu', 'dilayani', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunjung_kecamatan');
    }
};
