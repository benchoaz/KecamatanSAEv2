<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nama_role' => 'Super Admin',
                'deskripsi' => 'Akses penuh ke seluruh sistem, manajemen user, dan konfigurasi master.'
            ],
            [
                'nama_role' => 'Operator Kecamatan',
                'deskripsi' => 'Pengelola data wilayah kecamatan, monitoring desa, dan verifikasi adminstratif.'
            ],
            [
                'nama_role' => 'Operator Desa',
                'deskripsi' => 'Penginput data pembangunan dan administrasi tingkat desa.'
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(
                ['nama_role' => $role['nama_role']],
                ['deskripsi' => $role['deskripsi']]
            );
        }
    }
}
