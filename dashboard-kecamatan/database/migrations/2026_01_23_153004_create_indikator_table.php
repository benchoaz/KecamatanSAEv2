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
        Schema::create('indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspek_id')->constrained('aspek')->onDelete('cascade');
            $table->string('kode_indikator', 30); // PEM-01-001
            $table->string('nama_indikator', 500);
            $table->text('deskripsi')->nullable()->comment('Penjelasan cara pengisian');
            $table->enum('tipe_input', [
                'text',
                'number',
                'date',
                'select',
                'checkbox',
                'file',
                'textarea'
            ])->default('text');
            $table->json('opsi_select')->nullable()->comment('Untuk tipe select/checkbox');
            $table->string('satuan', 50)->nullable();
            $table->boolean('wajib_bukti')->default(false);
            $table->decimal('bobot', 5, 2)->default(0);
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
        Schema::dropIfExists('indikator');
    }
};
