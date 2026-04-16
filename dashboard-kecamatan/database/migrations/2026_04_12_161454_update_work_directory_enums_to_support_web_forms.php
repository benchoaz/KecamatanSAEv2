<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN job_type ENUM('harian', 'jasa', 'keliling', 'transportasi', 'umkm') NOT NULL");
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN status ENUM('active', 'inactive', 'pending') DEFAULT 'pending'");
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN data_source ENUM('kecamatan', 'desa', 'warga', 'web_form') DEFAULT 'kecamatan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN job_type ENUM('harian', 'jasa', 'keliling', 'transportasi') NOT NULL");
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        DB::statement("ALTER TABLE work_directory MODIFY COLUMN data_source ENUM('kecamatan', 'desa', 'warga') DEFAULT 'kecamatan'");
    }
};
