<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trantibum_relawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id')->index();
            $table->string('nama');
            $table->string('nik', 16)->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan')->default('Anggota'); // Ketua, Anggota
            $table->boolean('status_aktif')->default(true);
            $table->string('foto')->nullable();
            $table->string('sk_file')->nullable(); // Upload SK PDF
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trantibum_relawans');
    }
};
