<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personil_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('personil_desa', 'status')) {
                $table->string('status')->default('draft')->after('is_active');
            }
            if (!Schema::hasColumn('personil_desa', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable()->after('status');
            }
        });

        Schema::table('lembaga_desa', function (Blueprint $table) {
            if (!Schema::hasColumn('lembaga_desa', 'status')) {
                $table->string('status')->default('draft')->after('is_active');
            }
            if (!Schema::hasColumn('lembaga_desa', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('personil_desa', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_revisi']);
        });

        Schema::table('lembaga_desa', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_revisi']);
        });
    }
};
