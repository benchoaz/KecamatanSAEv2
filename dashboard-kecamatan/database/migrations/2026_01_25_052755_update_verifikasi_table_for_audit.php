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
        Schema::table('verifikasi', function (Blueprint $table) {
            $table->string('from_status')->after('verifikator_id')->nullable();
            $table->string('to_status')->after('from_status');
            $table->string('role')->after('to_status')->nullable();
            // We can keep the old status column for compatibility or drop it if we rely on to_status
            // For now, let's keep the existing structure but add these for clarity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi', function (Blueprint $table) {
            $table->dropColumn(['from_status', 'to_status', 'role']);
        });
    }
};
