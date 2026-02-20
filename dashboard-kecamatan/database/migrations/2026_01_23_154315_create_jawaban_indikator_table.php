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
        Schema::create('jawaban_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submission')->onDelete('cascade');
            $table->foreignId('indikator_id')->constrained('indikator')->onDelete('cascade');

            $table->text('nilai_text')->nullable();
            $table->decimal('nilai_number', 15, 2)->nullable();
            $table->date('nilai_date')->nullable();
            $table->json('nilai_select')->nullable();

            $table->decimal('skor', 5, 2)->default(0);

            $table->timestamps();

            $table->unique(['submission_id', 'indikator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_indikator');
    }
};
