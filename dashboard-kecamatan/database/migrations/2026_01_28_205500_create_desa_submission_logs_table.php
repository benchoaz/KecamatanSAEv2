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
        Schema::create('desa_submission_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('action'); // e.g., 'created', 'updated', 'submitted', 'returned', 'completed'
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa_submission_logs');
    }
};
