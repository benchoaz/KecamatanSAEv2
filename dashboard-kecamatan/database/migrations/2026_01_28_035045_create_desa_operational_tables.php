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
        // 1. Induk Laporan
        Schema::create('desa_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('desa_id')->index();

            $table->string('modul');
            $table->string('judul');
            $table->string('periode')->nullable();

            // Status Workflow
            $table->enum('status', ['draft', 'submitted', 'returned', 'completed'])->default('draft')->index();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->unsignedBigInteger('created_by');

            $table->timestamps();
        });

        // 2. Detail Data
        Schema::create('desa_submission_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_id')->index();

            $table->string('field_key');
            $table->longText('field_value')->nullable();

            $table->timestamps();
        });

        // 3. File Upload
        Schema::create('desa_submission_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_id')->index();

            $table->string('file_type');
            $table->string('file_path');

            $table->timestamps();
        });

        // 4. Catatan Pembinaan
        Schema::create('desa_submission_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_id')->index();

            $table->text('note');
            $table->unsignedBigInteger('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa_submission_notes');
        Schema::dropIfExists('desa_submission_files');
        Schema::dropIfExists('desa_submission_details');
        Schema::dropIfExists('desa_submissions');
    }
};
