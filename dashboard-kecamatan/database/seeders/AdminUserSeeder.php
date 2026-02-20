<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = \App\Models\Role::where('nama_role', 'Super Admin')->first();

        \App\Models\User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator Utama',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role_id' => $roleAdmin->id,
                'status' => 'aktif',
            ]
        );
    }
}
