<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->string('tatadesa_domain')->nullable(); // e.g. "alasnyiur.tatadesa.com"
            $table->string('website_url')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropColumn(['tatadesa_domain', 'website_url']);
        });
    }
};
