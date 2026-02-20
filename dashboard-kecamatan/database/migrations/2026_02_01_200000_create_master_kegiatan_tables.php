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
        // 1. Master Bidang (Level 1 - 5 Bidang APBDes)
        Schema::create('master_bidang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bidang', 20)->unique();
            $table->string('nama_bidang');
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Master Sub Bidang (Level 2)
        Schema::create('master_sub_bidang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bidang_id')->index();
            $table->string('kode_sub_bidang', 20)->unique();
            $table->string('nama_sub_bidang');
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('bidang_id')->references('id')->on('master_bidang')->onDelete('cascade');
        });

        // 3. Master Kegiatan (Level 3 - Pusat Logika)
        Schema::create('master_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_bidang_id')->index();
            $table->string('kode_kegiatan', 30)->unique();
            $table->string('nama_kegiatan');
            $table->enum('jenis_kegiatan', ['fisik', 'non_fisik', 'musdes', 'blt']);
            $table->string('satuan_default', 50)->nullable();
            $table->decimal('harga_referensi', 15, 2)->nullable()->comment('SSH/SBU referensi, tidak memblokir');
            $table->json('template_spj')->nullable()->comment('Template dokumen per jenis kegiatan');
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('sub_bidang_id')->references('id')->on('master_sub_bidang')->onDelete('cascade');
        });

        // 4. Master Komponen Belanja
        Schema::create('master_komponen_belanja', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komponen', 20)->unique();
            $table->string('nama_komponen');
            $table->enum('kategori', ['honor', 'konsumsi', 'barang', 'jasa', 'perjalanan']);
            $table->boolean('objek_pajak')->default(false);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_referensi', 15, 2)->nullable()->comment('SSH/SBU referensi');
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Pivot: Kegiatan - Komponen Belanja
        Schema::create('kegiatan_komponen_belanja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_kegiatan_id')->index();
            $table->unsignedBigInteger('master_komponen_belanja_id')->index();
            $table->boolean('is_wajib')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('master_kegiatan_id')->references('id')->on('master_kegiatan')->onDelete('cascade');
            $table->foreign('master_komponen_belanja_id')->references('id')->on('master_komponen_belanja')->onDelete('cascade');

            $table->unique(['master_kegiatan_id', 'master_komponen_belanja_id'], 'kegiatan_komponen_unique');
        });

        // 6. Add FK to pembangunan_desa
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->unsignedBigInteger('master_kegiatan_id')->nullable()->after('desa_id');
            $table->foreign('master_kegiatan_id')->references('id')->on('master_kegiatan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropForeign(['master_kegiatan_id']);
            $table->dropColumn('master_kegiatan_id');
        });

        Schema::dropIfExists('kegiatan_komponen_belanja');
        Schema::dropIfExists('master_komponen_belanja');
        Schema::dropIfExists('master_kegiatan');
        Schema::dropIfExists('master_sub_bidang');
        Schema::dropIfExists('master_bidang');
    }
};
