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
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->boolean('is_popular')->default(false)->after('is_active');
            $table->string('link_type')->default('form')->after('is_popular'); // form, loker, umkm, external
            $table->string('custom_link')->nullable()->after('link_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_layanan', function (Blueprint $table) {
            $table->dropColumn(['is_popular', 'link_type', 'custom_link']);
        });
    }
};
