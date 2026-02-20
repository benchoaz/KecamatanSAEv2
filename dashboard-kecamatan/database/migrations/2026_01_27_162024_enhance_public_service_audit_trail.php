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
            $table->boolean('is_agreed')->default(false)->after('whatsapp');
            $table->text('public_response')->nullable()->after('internal_notes');
            $table->timestamp('responded_at')->nullable()->after('public_response');
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
