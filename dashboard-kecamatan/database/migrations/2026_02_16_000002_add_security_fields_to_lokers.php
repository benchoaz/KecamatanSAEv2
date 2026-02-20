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
        Schema::table('lokers', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('status');
            $table->boolean('is_flagged')->default(false)->after('is_verified');
            $table->string('owner_pin', 60)->nullable()->after('is_flagged');
            $table->timestamp('last_toggle_at')->nullable()->after('owner_pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lokers', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'is_flagged', 'owner_pin', 'last_toggle_at']);
        });
    }
};