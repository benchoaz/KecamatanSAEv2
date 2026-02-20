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
        Schema::create('sub_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikator')->onDelete('cascade');
            $table->string('kode_sub', 40); // PEM-01-001-a
            $table->string('nama_sub', 500);
            $table->enum('tipe_input', [
                'text',
                'number',
                'date',
                'select',
                'checkbox',
                'file',
                'textarea'
            ])->default('text');
            $table->json('opsi_select')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_indikator');
    }
};
