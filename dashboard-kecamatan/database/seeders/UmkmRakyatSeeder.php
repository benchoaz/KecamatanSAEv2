<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Umkm;

class UmkmRakyatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $umkms = [
            [
                'nama_usaha' => 'Toko Kerupuk Pak Budi',
                'nama_pemilik' => 'Budi Santoso',
                'no_wa' => '6282231203765',
                'desa' => 'Desa Besuk',
                'jenis_usaha' => 'Kerupuk',
                'deskripsi' => 'Produksi kerupuk手工 dengan bahan berkualitas',
                'status' => Umkm::STATUS_AKTIF,
                'source' => Umkm::SOURCE_ADMIN
            ],
            [
                'nama_usaha' => 'Toko Madu Asli Besuk',
                'nama_pemilik' => 'Siti Nurhaliza',
                'no_wa' => '6281234567890',
                'desa' => 'Desa Sidomulyo',
                'jenis_usaha' => 'Madu',
                'deskripsi' => 'Madu murni dari peternakan lebah lokal',
                'status' => Umkm::STATUS_AKTIF,
                'source' => Umkm::SOURCE_ADMIN
            ],
            [
                'nama_usaha' => 'UMKM Bakso Pak Slamet',
                'nama_pemilik' => 'Slamet Sutrisno',
                'no_wa' => '6285678901234',
                'desa' => 'Desa Besuk',
                'jenis_usaha' => 'Bakso',
                'deskripsi' => 'Bakso enak dengan resep turun temurun',
                'status' => Umkm::STATUS_AKTIF,
                'source' => Umkm::SOURCE_ADMIN
            ],
            [
                'nama_usaha' => 'Tukang Piket Pak Joko',
                'nama_pemilik' => 'Joko Susilo',
                'no_wa' => '6282231203765',
                'desa' => 'Desa Sumberrejo',
                'jenis_usaha' => 'Jasa Piket',
                'deskripsi' => 'Jasa tukang piket berpengalaman',
                'status' => Umkm::STATUS_AKTIF,
                'source' => Umkm::SOURCE_ADMIN
            ],
            [
                'nama_usaha' => 'Service AC Pak Rudi',
                'nama_pemilik' => 'Rudi Hartono',
                'no_wa' => '6281345678901',
                'desa' => 'Desa Karangrejo',
                'jenis_usaha' => 'Service AC',
                'deskripsi' => 'Service AC profesional dengan garansi',
                'status' => Umkm::STATUS_AKTIF,
                'source' => Umkm::SOURCE_ADMIN
            ]
        ];

        foreach ($umkms as $umkm) {
            Umkm::updateOrCreate(
                ['nama_usaha' => $umkm['nama_usaha']],
                $umkm
            );
        }

        $this->command->info('UMKM Rakyat data seeded successfully!');
    }
}
