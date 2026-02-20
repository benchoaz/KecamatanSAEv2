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
            $table->string('nik', 16)->nullable()->after('nama_pemohon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
