<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add to umkm
        Schema::table('umkm', function (Blueprint $table) {
            $table->timestamp('name_updated_at')->nullable()->after('nama_usaha');
        });

        // Add to work_directory
        Schema::table('work_directory', function (Blueprint $table) {
            $table->timestamp('name_updated_at')->nullable()->after('job_title');
        });

        // Add to umkm_locals
        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->timestamp('name_updated_at')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropColumn('name_updated_at');
        });

        Schema::table('work_directory', function (Blueprint $table) {
            $table->dropColumn('name_updated_at');
        });

        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->dropColumn('name_updated_at');
        });
    }
};
