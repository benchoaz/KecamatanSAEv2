<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembangunan_logbooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembangunan_desa_id')->index();
            $table->integer('progres_fisik'); // persentase 0-100
            $table->text('catatan')->nullable();
            $table->text('kendala')->nullable();
            $table->string('foto_progres')->nullable();
            $table->timestamps();

            $table->foreign('pembangunan_desa_id')->references('id')->on('pembangunan_desa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembangunan_logbooks');
    }
};
