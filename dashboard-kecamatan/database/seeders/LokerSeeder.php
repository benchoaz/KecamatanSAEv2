<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loker;

class LokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokers = [
            [
                'title' => 'Sopir Truk',
                'job_category' => 'Transportasi',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Besuk',
                'contact_wa' => '6282231203765',
                'description' => 'Butuh sopir truk untuk pengiriman barang ke luar kota. Syarat: memiliki SIM A, berpengalaman minimal 2 tahun.',
                'work_time' => 'Harian',
                'is_available_today' => true,
                'status' => Loker::STATUS_AKTIF,
                'is_sensitive' => false,
                'source' => 'web_form',
                'is_verified' => true,
                'is_flagged' => false
            ],
            [
                'title' => 'Admin Toko',
                'job_category' => 'Administrasi',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Sidomulyo',
                'contact_wa' => '6281234567890',
                'description' => 'Lowongan admin toko grosir. Tanggung jawab: input data barang, manajemen stok, layanan pelanggan.',
                'work_time' => 'Harian',
                'is_available_today' => true,
                'status' => Loker::STATUS_AKTIF,
                'is_sensitive' => false,
                'source' => 'admin_input',
                'is_verified' => true,
                'is_flagged' => false
            ],
            [
                'title' => 'Karyawan Restoran',
                'job_category' => 'Food & Beverage',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Sumberrejo',
                'contact_wa' => '6285678901234',
                'description' => 'Butuh karyawan restoran untuk bagian dapur dan pelayan. Bersedia belajar dan berkerja keras.',
                'work_time' => 'Shift',
                'is_available_today' => true,
                'status' => Loker::STATUS_AKTIF,
                'is_sensitive' => false,
                'source' => 'whatsapp',
                'is_verified' => true,
                'is_flagged' => false
            ],
            [
                'title' => 'Teknisi Listrik',
                'job_category' => 'Teknik',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Karangrejo',
                'contact_wa' => '6281345678901',
                'description' => 'Lowongan teknisi listrik untuk perbaikan dan instalasi listrik di rumah tangga. Memiliki sertifikat keahlian.',
                'work_time' => 'Harian',
                'is_available_today' => true,
                'status' => Loker::STATUS_AKTIF,
                'is_sensitive' => false,
                'source' => 'admin_input',
                'is_verified' => true,
                'is_flagged' => false
            ]
        ];

        foreach ($lokers as $loker) {
            Loker::updateOrCreate(
                ['title' => $loker['title']],
                $loker
            );
        }

        $this->command->info('Loker data seeded successfully!');
    }
}
