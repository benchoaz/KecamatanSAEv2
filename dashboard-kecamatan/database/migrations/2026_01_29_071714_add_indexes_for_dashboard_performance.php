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
        Schema::table('audit_log', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('user_id');
            $table->index('table_name');
        });

        Schema::table('submission', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['desa_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['table_name']);
        });

        Schema::table('submission', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['desa_id', 'status']);
        });
    }
};
