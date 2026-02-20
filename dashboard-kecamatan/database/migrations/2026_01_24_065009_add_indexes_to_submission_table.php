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
        Schema::table('submission', function (Blueprint $table) {
            // Index for dashboard filtering by Desa + Status
            $table->index(['desa_id', 'status'], 'idx_submission_desa_status');

            // Index for sorting by time (latest()) and filtering by time
            $table->index('created_at', 'idx_submission_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission', function (Blueprint $table) {
            $table->dropIndex('idx_submission_desa_status');
            $table->dropIndex('idx_submission_created_at');
        });
    }
};
