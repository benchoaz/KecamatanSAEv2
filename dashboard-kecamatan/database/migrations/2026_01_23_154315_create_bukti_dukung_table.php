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
        Schema::create('bukti_dukung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submission')->onDelete('cascade');
            $table->foreignId('indikator_id')->nullable()->constrained('indikator')->onDelete('cascade');

            $table->enum('tipe_file', ['pdf', 'image', 'doc', 'xls', 'other'])->default('image');
            $table->string('nama_file', 255);
            $table->string('path_file', 500);
            $table->bigInteger('ukuran_bytes')->default(0);
            $table->text('deskripsi')->nullable();

            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();

            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_dukung');
    }
};
