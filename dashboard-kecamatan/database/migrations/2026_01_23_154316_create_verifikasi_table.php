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
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submission')->onDelete('cascade');
            $table->foreignId('verifikator_id')->constrained('users');

            $table->enum('tipe_verifikasi', ['kasi', 'lintas_sektor', 'camat']);
            $table->enum('status', ['approved', 'revision', 'rejected']);
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};
