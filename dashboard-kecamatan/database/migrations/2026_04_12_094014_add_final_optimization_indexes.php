<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // UMKM Table Indexes
        if (Schema::hasTable("umkms")) {
            try {
                Schema::table("umkms", function (Blueprint $table) {
                    $table->index("status", "idx_umkm_status");
                    $table->index("is_verified", "idx_umkm_verified");
                    $table->index("desa_id", "idx_umkm_desa_id");
                    $table->index("nik", "idx_umkm_nik");
                });
            } catch (\Exception $e) { \Log::warning("idx umkms skipped: " . $e->getMessage()); }
        }

        // UMKM Products Table Indexes
        if (Schema::hasTable("umkm_products")) {
            try {
                Schema::table("umkm_products", function (Blueprint $table) {
                    $table->index("umkm_id", "idx_product_umkm_id");
                    $table->index("is_active", "idx_product_active");
                    $table->index("category", "idx_product_category");
                });
            } catch (\Exception $e) { \Log::warning("idx umkm_products skipped: " . $e->getMessage()); }
        }

        // Work Directory Table Indexes
        if (Schema::hasTable("work_directories")) {
            try {
                Schema::table("work_directories", function (Blueprint $table) {
                    $table->index("status", "idx_work_status");
                    $table->index("village_id", "idx_work_village");
                    $table->index("is_active", "idx_work_active");
                });
            } catch (\Exception $e) { \Log::warning("idx work_directories skipped: " . $e->getMessage()); }
        }
    }

    public function down(): void
    {
        Schema::table("umkms", function (Blueprint $table) {
            $table->dropIndex("idx_umkm_status");
            $table->dropIndex("idx_umkm_verified");
            $table->dropIndex("idx_umkm_desa_id");
            $table->dropIndex("idx_umkm_nik");
        });

        Schema::table("umkm_products", function (Blueprint $table) {
            $table->dropIndex("idx_product_umkm_id");
            $table->dropIndex("idx_product_active");
            $table->dropIndex("idx_product_category");
        });

        Schema::table("work_directories", function (Blueprint $table) {
            $table->dropIndex("idx_work_status");
            $table->dropIndex("idx_work_village");
            $table->dropIndex("idx_work_active");
        });
    }
};
