<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UmkmLocal;

class UmkmLocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $umkms = [
            // UMKM examples
            [
                'name' => 'Toko Kerupuk Pak Budi',
                'product' => 'Kerupuk Kentang, Kerupuk Udang, Kerupuk Pisang',
                'address' => 'Jl. Raya Besuk No. 123, Desa Besuk',
                'price' => 15000.00,
                'original_price' => 18000.00,
                'description' => 'Produksi kerupuk手工 dengan bahan berkualitas',
                'contact_wa' => '6282231203765',
                'is_active' => true,
                'is_featured' => false,
                'is_verified' => true,
                'is_flagged' => false,
                'module' => UmkmLocal::MODULE_UMKM
            ],
            [
                'name' => 'Toko Madu Asli Besuk',
                'product' => 'Madu Kelulut, Madu Tebu, Madu Hutan',
                'address' => 'Jl. Kampung Madu No. 45, Desa Sidomulyo',
                'price' => 50000.00,
                'original_price' => 60000.00,
                'description' => 'Madu murni dari peternakan lebah lokal',
                'contact_wa' => '6281234567890',
                'is_active' => true,
                'is_featured' => true,
                'is_verified' => true,
                'is_flagged' => false,
                'module' => UmkmLocal::MODULE_UMKM
            ],
            [
                'name' => 'UMKM Bakso Pak Slamet',
                'product' => 'Bakso Sapi, Bakso Ayam, Bakso Campur',
                'address' => 'Jl. Pasar Besuk No. 78, Desa Besuk',
                'price' => 10000.00,
                'original_price' => 12000.00,
                'description' => 'Bakso enak dengan resep turun temurun',
                'contact_wa' => '6285678901234',
                'is_active' => true,
                'is_featured' => false,
                'is_verified' => true,
                'is_flagged' => false,
                'module' => UmkmLocal::MODULE_UMKM
            ],
            // Jasa examples
            [
                'name' => 'Tukang Piket Pak Joko',
                'product' => 'Jasa Piket Rumah, Keamanan Tempat Usaha',
                'address' => 'Jl. Kebun Raya No. 34, Desa Sumberrejo',
                'price' => 20000.00,
                'original_price' => 25000.00,
                'description' => 'Jasa tukang piket berpengalaman',
                'contact_wa' => '6282231203765',
                'is_active' => true,
                'is_featured' => false,
                'is_verified' => true,
                'is_flagged' => false,
                'module' => UmkmLocal::MODULE_JASA
            ],
            [
                'name' => 'Service AC Pak Rudi',
                'product' => 'Service AC, Perbaikan AC, Penjualan Spare Part',
                'address' => 'Jl. Industri No. 56, Desa Karangrejo',
                'price' => 50000.00,
                'original_price' => 60000.00,
                'description' => 'Service AC profesional dengan garansi',
                'contact_wa' => '6281345678901',
                'is_active' => true,
                'is_featured' => true,
                'is_verified' => true,
                'is_flagged' => false,
                'module' => UmkmLocal::MODULE_JASA
            ]
        ];

        foreach ($umkms as $umkm) {
            UmkmLocal::updateOrCreate(
                ['name' => $umkm['name']],
                $umkm
            );
        }

        $this->command->info('UMKM and Jasa data seeded successfully!');
    }
}
