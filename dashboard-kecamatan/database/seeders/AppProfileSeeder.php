<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AppProfile::updateOrCreate(
            ['id' => 1],
            [
                'app_name' => 'Kecamatan SAE',
                'region_name' => 'Kecamatan Besuk',
                'region_level' => 'kecamatan',
                'tagline' => 'Solusi Administrasi Terpadu untuk Masyarakat',
            ]
        );
    }
}
