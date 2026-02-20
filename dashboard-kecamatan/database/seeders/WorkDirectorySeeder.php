<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkDirectory;

class WorkDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Jasa & Pekerjaan Harian
            [
                'display_name' => 'Pak Roni',
                'job_category' => 'Jasa & Pekerjaan Harian',
                'job_type' => 'jasa',
                'job_title' => 'Tukang Pijat Refleksi & Tradisional',
                'service_area' => 'Desa Ketompen & Sekitarnya',
                'service_time' => 'Berdasarkan panggilan (24 Jam)',
                'contact_phone' => '081234567890',
                'short_description' => 'Menerima pijat tradisional untuk kesehatan dan relaksasi. Berpengalaman lebih dari 10 tahun.',
                'data_source' => 'kecamatan',
                'status' => 'active',
            ],
            [
                'display_name' => 'Mas Budi',
                'job_category' => 'Jasa & Pekerjaan Harian',
                'job_type' => 'harian',
                'job_title' => 'Buruh Tani (Tanam & Panen)',
                'service_area' => 'Wilayah Kecamatan Besuk',
                'service_time' => 'Musim Tanam & Panen (06.00 - 12.00)',
                'contact_phone' => '085234567811',
                'short_description' => 'Siap melayani jasa tanam padi, jagung, dan tenaga panen. Bisa beregu (borongan).',
                'data_source' => 'desa',
                'status' => 'active',
            ],
            [
                'display_name' => 'Pak Herman',
                'job_category' => 'Jasa & Pekerjaan Harian',
                'job_type' => 'jasa',
                'job_title' => 'Tukang Bangunan & Renovasi',
                'service_area' => 'Kecamatan Besuk & Kraksaan',
                'service_time' => 'Senin - Sabtu (08.00 - 16.00)',
                'contact_phone' => '087234567822',
                'short_description' => 'Spesialis pasang keramik, cat dinding, dan perbaikan atap bocor. Hasil rapi dan jujur.',
                'data_source' => 'kecamatan',
                'status' => 'active',
            ],

            // Transportasi Rakyat
            [
                'display_name' => 'Bang Jay',
                'job_category' => 'Transportasi Rakyat',
                'job_type' => 'transportasi',
                'job_title' => 'Ojek Pangkalan & Kurir Lokal',
                'service_area' => 'Pangkalan Pasar Besuk',
                'service_time' => 'Setiap Hari (05.00 - 20.00)',
                'contact_phone' => '089234567833',
                'short_description' => 'Antar jemput warga, sekolah, dan jasa titip beli barang di pasar.',
                'data_source' => 'warga',
                'status' => 'active',
            ],
            [
                'display_name' => 'Pak Slamet',
                'job_category' => 'Transportasi Rakyat',
                'job_type' => 'transportasi',
                'job_title' => 'Sopir Pick-up Angkutan Barang',
                'service_area' => 'Antar Desa / Luar Kecamatan',
                'service_time' => '24 Jam (Sesuai Janji)',
                'contact_phone' => '081234567844',
                'short_description' => 'Melayani angkutan hasil tani, pindahan rumah, dan kirim material.',
                'data_source' => 'kecamatan',
                'status' => 'active',
            ],

            // Jasa & Pangan Keliling
            [
                'display_name' => 'Bu Siti',
                'job_category' => 'Jasa & Pangan Keliling',
                'job_type' => 'keliling',
                'job_title' => 'Tukang Sayur Keliling',
                'service_area' => 'Desa Besuk Kidul & Desa Matekan',
                'service_time' => 'Pagi (06.00 - 10.00)',
                'contact_phone' => '082234567855',
                'short_description' => 'Sayur segar, lauk pauk, dan kebutuhan dapur harian lainnya.',
                'data_source' => 'desa',
                'status' => 'active',
            ],
            [
                'display_name' => 'Cak Ali',
                'job_category' => 'Jasa & Pangan Keliling',
                'job_type' => 'keliling',
                'job_title' => 'Penjual Gas & Air Galon Keliling',
                'service_area' => 'Wilayah Kecamatan Besuk',
                'service_time' => 'Setiap Hari (07.00 - 17.00)',
                'contact_phone' => '083234567866',
                'short_description' => 'Siap antar gas LPG 3kg dan air galon langsung ke depan pintu rumah.',
                'data_source' => 'warga',
                'status' => 'active',
            ],
        ];

        foreach ($data as $item) {
            WorkDirectory::create($item);
        }
    }
}
