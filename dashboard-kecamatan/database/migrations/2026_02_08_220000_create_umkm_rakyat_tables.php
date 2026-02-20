<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 📘 Tabel: umkm
        Schema::create('umkm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('no_wa');
            $table->string('desa');
            $table->string('jenis_usaha');
            $table->text('deskripsi')->nullable();
            $table->string('foto_usaha')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
            $table->enum('source', ['admin', 'self-service'])->default('self-service');
            $table->string('slug')->unique();
            $table->string('manage_token')->unique();
            $table->timestamps();
        });

        // 📘 Tabel: umkm_verification
        Schema::create('umkm_verification', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->string('kode_verifikasi');
            $table->timestamp('expired_at');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        // 📘 Tabel: umkm_products
        Schema::create('umkm_products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->string('nama_produk');
            $table->decimal('harga', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->string('foto_produk')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 📘 Tabel: umkm_admin_log
        Schema::create('umkm_admin_log', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->enum('action', ['create', 'verify', 'deactivate', 'activate']);
            $table->enum('actor', ['system', 'admin']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_admin_log');
        Schema::dropIfExists('umkm_products');
        Schema::dropIfExists('umkm_verification');
        Schema::dropIfExists('umkm');
    }
};
