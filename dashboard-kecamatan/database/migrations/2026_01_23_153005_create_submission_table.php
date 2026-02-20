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
        Schema::create('submission', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('desa_id')->constrained('desa')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->foreignId('aspek_id')->constrained('aspek')->onDelete('cascade');
            $table->year('tahun');
            $table->enum('periode', ['bulanan', 'triwulan', 'semester', 'tahunan']);
            $table->tinyInteger('bulan')->nullable();
            $table->tinyInteger('triwulan')->nullable();
            $table->tinyInteger('semester')->nullable();

            // Backdate fields
            $table->boolean('is_backdate')->default(false);
            $table->text('alasan_backdate')->nullable();
            $table->unsignedBigInteger('approved_backdate_by')->nullable(); // Camat
            $table->timestamp('approved_backdate_at')->nullable();

            $table->enum('status', ['draft', 'ready', 'submitted', 'returned', 'resubmitted', 'approved'])->default('draft');
            $table->text('catatan_desa')->nullable();

            $table->foreignId('submitted_by')->constrained('users'); // Operator
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedBigInteger('reviewed_by')->nullable(); // Kasi
            $table->timestamp('reviewed_at')->nullable();
            $table->text('catatan_review')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable(); // Camat
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_approval')->nullable();

            $table->decimal('skor_total', 5, 2)->nullable();

            $table->timestamps();

            // Index untuk mencegah duplikasi submission
            $table->unique(['desa_id', 'aspek_id', 'tahun', 'periode', 'bulan', 'triwulan', 'semester'], 'sub_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission');
    }
};
