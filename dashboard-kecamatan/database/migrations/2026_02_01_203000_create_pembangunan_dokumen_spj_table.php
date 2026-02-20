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
        // 1. Create Pembangunan Dokumen SPJ table
        Schema::create('pembangunan_dokumen_spj', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembangunan_desa_id')->index();
            $table->unsignedBigInteger('master_dokumen_spj_id')->index();

            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'uploaded', 'verified'])->default('pending');
            $table->boolean('is_wajib')->default(true);

            $table->dateTime('uploaded_at')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();

            $table->text('catatan_verifikator')->nullable();
            $table->timestamps();

            $table->foreign('pembangunan_desa_id')->references('id')->on('pembangunan_desa')->onDelete('cascade');
            $table->foreign('master_dokumen_spj_id')->references('id')->on('master_dokumen_spj')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Adjust Pembangunan Desa table to align with the new standard
        Schema::table('pembangunan_desa', function (Blueprint $table) {
            // Change status_kegiatan to enum if possible, or at least standardized
            // We use string for now to avoid data loss, but we will standardize the values in model
            $table->string('status_kegiatan')->default('belum')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembangunan_dokumen_spj');
    }
};
