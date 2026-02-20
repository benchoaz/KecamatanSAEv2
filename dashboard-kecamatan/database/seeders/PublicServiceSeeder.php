<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PublicService;

class PublicServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'uuid' => 'test-service-001',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Besuk',
                'jenis_layanan' => 'Surat Keterangan Domisili',
                'uraian' => 'Permohonan surat keterangan domisili untuk keperluan administrasi',
                'whatsapp' => '6282231203765',
                'status' => PublicService::STATUS_DIPROSES,
                'ip_address' => '192.168.1.100',
                'category' => PublicService::CATEGORY_PELAYANAN,
                'source' => 'web_form'
            ],
            [
                'uuid' => 'test-service-002',
                'desa_id' => null,
                'nama_desa_manual' => 'Desa Sidomulyo',
                'jenis_layanan' => 'Surat Pengantar Nikah',
                'uraian' => 'Permohonan surat pengantar nikah untuk calon pengantin',
                'whatsapp' => '6281234567890',
                'status' => PublicService::STATUS_SELESAI,
                'ip_address' => '192.168.1.101',
                'category' => PublicService::CATEGORY_PELAYANAN,
                'source' => 'whatsapp'
            ]
        ];

        foreach ($services as $service) {
            PublicService::updateOrCreate(
                ['uuid' => $service['uuid']],
                $service
            );
        }

        $this->command->info('Public service data seeded successfully!');
    }
}
