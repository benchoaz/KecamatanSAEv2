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
        Schema::table('umkm', function (Blueprint $table) {
            $table->string('operating_hours')->nullable()->after('shopee_url');
            $table->boolean('is_on_holiday')->default(false)->after('operating_hours');
        });

        Schema::table('work_directory', function (Blueprint $table) {
            $table->string('operating_hours')->nullable()->after('service_time');
            $table->boolean('is_on_holiday')->default(false)->after('operating_hours');
        });

        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->string('operating_hours')->nullable()->after('module');
            $table->boolean('is_on_holiday')->default(false)->after('operating_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropColumn(['operating_hours', 'is_on_holiday']);
        });

        Schema::table('work_directory', function (Blueprint $table) {
            $table->dropColumn(['operating_hours', 'is_on_holiday']);
        });

        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->dropColumn(['operating_hours', 'is_on_holiday']);
        });
    }
};
