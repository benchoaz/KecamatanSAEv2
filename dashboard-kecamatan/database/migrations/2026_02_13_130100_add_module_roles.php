<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert module-specific roles
        $now = now();

        $roles = [
            [
                'nama_role' => 'trantibum_admin',
                'deskripsi' => 'Administrator Modul Trantibum & Linmas - Akses penuh ke fitur ketentraman dan ketertiban umum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_role' => 'umkm_admin',
                'deskripsi' => 'Administrator Modul UMKM - Akses penuh ke fitur pengelolaan UMKM dan etalase',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_role' => 'loker_admin',
                'deskripsi' => 'Administrator Modul Loker - Akses penuh ke fitur lowongan kerja',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_role' => 'pelayanan_admin',
                'deskripsi' => 'Administrator Modul Pelayanan - Akses penuh ke fitur pelayanan publik',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Check if roles already exist before inserting
        foreach ($roles as $role) {
            $exists = DB::table('roles')->where('nama_role', $role['nama_role'])->exists();
            if (!$exists) {
                DB::table('roles')->insert($role);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove module-specific roles
        DB::table('roles')
            ->whereIn('nama_role', ['trantibum_admin', 'umkm_admin', 'loker_admin', 'pelayanan_admin'])
            ->delete();
    }
};
