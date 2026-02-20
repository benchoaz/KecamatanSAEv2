<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            if (!Schema::hasColumn('public_services', 'category')) {
                $table->string('category')->default('pengaduan')->after('uuid');
                // category: pengaduan, pelayanan, umkm, loker, skm
            }
            if (!Schema::hasColumn('public_services', 'source')) {
                $table->string('source')->default('web_form')->after('category');
                // source: web_form, chatbox, whatsapp, admin_input
            }
        });
    }

    public function down(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            $table->dropColumn(['category', 'source']);
        });
    }
};
