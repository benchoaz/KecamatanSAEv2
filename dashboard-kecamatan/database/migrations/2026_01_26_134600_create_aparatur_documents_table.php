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
        Schema::create('aparatur_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('aparatur_desa_id')->constrained('aparatur_desa')->onDelete('cascade');
            $table->string('document_type', 50)->comment('SK_PENGANGKATAN, SK_PEMBERHENTIAN, DLL');
            $table->string('file_path');
            $table->string('original_filename');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aparatur_documents');
    }
};
