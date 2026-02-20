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
        // 1. Master SSH (Standar Satuan Harga - Rentang Harga Wajar)
        Schema::create('master_ssh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komponen_belanja_id')->index();
            $table->integer('tahun');
            $table->string('wilayah', 100);
            $table->string('satuan', 50);
            $table->decimal('harga_wajar_min', 15, 2);
            $table->decimal('harga_wajar_max', 15, 2);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('komponen_belanja_id')->references('id')->on('master_komponen_belanja')->onDelete('cascade');
        });

        // 2. Master SBU (Standar Biaya Umum - Batas Maksimum)
        Schema::create('master_sbu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komponen_belanja_id')->index();
            $table->integer('tahun');
            $table->string('wilayah', 100);
            $table->string('satuan', 50);
            $table->decimal('batas_maks', 15, 2);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('komponen_belanja_id')->references('id')->on('master_komponen_belanja')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sbu');
        Schema::dropIfExists('master_ssh');
    }
};
