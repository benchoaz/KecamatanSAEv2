<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VillageSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
                // MenuSeeder::class,
                // AspekSeeder::class,
                // IndikatorSeeder::class,
            BeritaSeeder::class,
            WorkDirectorySeeder::class,
            PelayananFaqSeeder::class,
            UmkmLocalSeeder::class,
            UmkmRakyatSeeder::class,
            LokerSeeder::class,
            PublicServiceSeeder::class,
            WhatsAppSettingsSeeder::class,
            WahaN8nSeeder::class,
        ]);
    }
}
