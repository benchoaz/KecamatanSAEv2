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
        Schema::create('desa_pagu_anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa')->onDelete('cascade');
            $table->integer('tahun');
            $table->string('sumber_dana'); // DDS, ADD, PBP, DLL
            $table->decimal('jumlah_pagu', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['desa_id', 'tahun', 'sumber_dana'], 'idx_desa_tahun_sumber');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa_pagu_anggaran');
    }
};
