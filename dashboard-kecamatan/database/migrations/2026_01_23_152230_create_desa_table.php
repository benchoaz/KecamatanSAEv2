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
        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_desa', 20)->unique()->index()->comment('Kode Desa Kemendagri');
            $table->string('nama_desa', 100);
            $table->string('kecamatan', 100)->default('Besuk');
            $table->string('kabupaten', 100)->default('Probolinggo');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->text('alamat_kantor')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true); // Keeping for backward compatibility if needed, but 'status' is primary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa');
    }
};
