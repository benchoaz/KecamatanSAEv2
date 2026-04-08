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
            if (!Schema::hasColumn('public_services', 'whatsapp_suffix')) {
                $table->string('whatsapp_suffix', 10)->nullable()->after('whatsapp')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            $table->dropColumn('whatsapp_suffix');
        });
    }
};
