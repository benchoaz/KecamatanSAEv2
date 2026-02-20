<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trantibum_kejadians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id')->index();
            $table->string('jenis_kejadian');
            $table->dateTime('waktu_kejadian');
            $table->string('lokasi_koordinat')->nullable();
            $table->text('lokasi_deskripsi')->nullable();
            $table->text('kronologi');
            $table->text('dampak_kerusakan')->nullable();
            $table->text('kondisi_mutakhir')->nullable();
            $table->text('upaya_penanganan')->nullable();
            $table->text('pihak_terlibat')->nullable();
            $table->string('status')->default('dilaporkan'); // dilaporkan, ditangani, selesai
            $table->string('foto_kejadian')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trantibum_kejadians');
    }
};
