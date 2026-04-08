<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Menambahkan owner_user_id untuk menghubungkan listing dengan User account
     */
    public function up(): void
    {
        // Tambahkan ke tabel umkm
        if (Schema::hasTable('umkm')) {
            Schema::table('umkm', function (Blueprint $table) {
                $table->foreignId('owner_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('status');
            });
        }

        // Tambahkan ke tabel loker
        if (Schema::hasTable('loker')) {
            Schema::table('loker', function (Blueprint $table) {
                $table->foreignId('owner_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('status');
            });
        }

        // Tambahkan ke tabel umkm_local
        if (Schema::hasTable('umkm_local')) {
            Schema::table('umkm_local', function (Blueprint $table) {
                $table->foreignId('owner_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('module');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
        });

        Schema::table('loker', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
        });

        Schema::table('umkm_local', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
        });
    }
};
