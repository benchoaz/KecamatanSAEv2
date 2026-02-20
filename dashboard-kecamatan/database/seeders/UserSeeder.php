<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@probolinggokab.go.id'],
            [
                'name' => 'Super Admin Kecamatan',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // 2. Camat
        DB::table('users')->updateOrInsert(
            ['email' => 'camat@probolinggokab.go.id'],
            [
                'name' => 'Camat SAE',
                'password' => Hash::make('password'),
                'role' => 'camat',
                'is_active' => true,
            ]
        );

        // 3. Kasi (Per Bidang)
        $kasis = [
            ['name' => 'Kasi Pemerintahan', 'email' => 'kasipem@probolinggokab.go.id', 'role' => 'kasi_pem'],
            ['name' => 'Kasi Ekbang', 'email' => 'kasiekbang@probolinggokab.go.id', 'role' => 'kasi_ekbang'],
            ['name' => 'Kasi Kesra', 'email' => 'kasikesra@probolinggokab.go.id', 'role' => 'kasi_kesra'],
            ['name' => 'Kasi Trantibum', 'email' => 'kasitantrib@probolinggokab.go.id', 'role' => 'kasi_trantibum'],
        ];

        foreach ($kasis as $kasi) {
            DB::table('users')->updateOrInsert(
                ['email' => $kasi['email']],
                [
                    'name' => $kasi['name'],
                    'password' => Hash::make('password'),
                    'role' => $kasi['role'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Operator Desa & Kepala Desa (Sample: Alas Kandang - 3513062001)
        // Get Desa ID
        $desaId = DB::table('desa')->where('kode_desa', '3513062001')->value('id');

        if ($desaId) {
            // Operator Desa
            DB::table('users')->updateOrInsert(
                ['email' => 'operator.alaskandang@probolinggokab.go.id'],
                [
                    'name' => 'Operator Alas Kandang',
                    'password' => Hash::make('password'),
                    'role' => 'operator_desa',
                    'desa_id' => $desaId,
                    'is_active' => true,
                ]
            );

            // Kepala Desa
            DB::table('users')->updateOrInsert(
                ['email' => 'kades.alaskandang@probolinggokab.go.id'],
                [
                    'name' => 'Kades Alas Kandang',
                    'password' => Hash::make('password'),
                    'role' => 'kades',
                    'desa_id' => $desaId,
                    'is_active' => true,
                ]
            );
        }
    }
}
