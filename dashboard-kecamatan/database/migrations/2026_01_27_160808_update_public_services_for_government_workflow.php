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
        Schema::table('public_services', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'otp_attempts', 'is_verified']);
            $table->unsignedBigInteger('handled_by')->nullable()->after('status');
            $table->text('internal_notes')->nullable()->after('handled_by');
            $table->timestamp('handled_at')->nullable()->after('internal_notes');

            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            //
        });
    }
};
