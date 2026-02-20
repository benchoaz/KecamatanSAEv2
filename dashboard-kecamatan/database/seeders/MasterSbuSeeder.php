<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSbuSeeder extends Seeder
{
    /**
     * Seed SBU (Standar Biaya Umum) untuk honorarium.
     */
    public function run(): void
    {
        $compIds = [
            'HON.01' => DB::table('master_komponen_belanja')->where('kode_komponen', 'HON.01')->value('id'), // Honor Narasumber
            'HON.03' => DB::table('master_komponen_belanja')->where('kode_komponen', 'HON.03')->value('id'), // Uang Saku
            'PJL.01' => DB::table('master_komponen_belanja')->where('kode_komponen', 'PJL.01')->value('id'), // Transport
        ];

        $compIds['HON.04'] = DB::table('master_komponen_belanja')->where('kode_komponen', 'HON.04')->value('id'); // Upah Tenaga Kerja

        $sbuData = [
            [
                'komponen_belanja_id' => $compIds['HON.01'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'OJ',
                'batas_maks' => 1000000,
                'keterangan' => 'Honorarium Narasumber (Perbup 5/2025)',
            ],
            [
                'komponen_belanja_id' => $compIds['HON.03'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'OH',
                'batas_maks' => 150000,
                'keterangan' => 'Uang Saku Peserta (Perbup 5/2025)',
            ],
            [
                'komponen_belanja_id' => $compIds['HON.04'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'bulan',
                'batas_maks' => 2989407,
                'keterangan' => 'Acuan UMK Probolinggo 2025',
            ],
            [
                'komponen_belanja_id' => $compIds['PJL.01'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'orang',
                'batas_maks' => 50000,
                'keterangan' => 'Transport Lokal (Perbup 5/2025)',
            ],
        ];

        foreach ($sbuData as $data) {
            if ($data['komponen_belanja_id']) {
                DB::table('master_sbu')->insert(array_merge($data, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
