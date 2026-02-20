<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSshSeeder extends Seeder
{
    /**
     * Seed SSH (Standar Satuan Harga) untuk barang/material.
     */
    public function run(): void
    {
        $compIds = [
            'BRG.06' => DB::table('master_komponen_belanja')->where('kode_komponen', 'BRG.06')->value('id'), // Semen
            'BRG.07' => DB::table('master_komponen_belanja')->where('kode_komponen', 'BRG.07')->value('id'), // Pasir
            'BRG.08' => DB::table('master_komponen_belanja')->where('kode_komponen', 'BRG.08')->value('id'), // Batu Belah
            'KON.01' => DB::table('master_komponen_belanja')->where('kode_komponen', 'KON.01')->value('id'), // Konsumsi Rapat
        ];

        $sshData = [
            [
                'komponen_belanja_id' => $compIds['BRG.06'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'sak',
                'harga_wajar_min' => 75000,
                'harga_wajar_max' => 85000,
                'keterangan' => 'Semen PC 40kg (Ref: Perbup 5/2025)',
            ],
            [
                'komponen_belanja_id' => $compIds['BRG.07'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'm3',
                'harga_wajar_min' => 250000,
                'harga_wajar_max' => 300000,
                'keterangan' => 'Pasir Pasang (Ref: Perbup 5/2025)',
            ],
            [
                'komponen_belanja_id' => $compIds['BRG.08'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'm3',
                'harga_wajar_min' => 350000,
                'harga_wajar_max' => 450000,
                'keterangan' => 'Batu Belah (Ref: Perbup 5/2025)',
            ],
            [
                'komponen_belanja_id' => $compIds['KON.01'],
                'tahun' => 2025,
                'wilayah' => 'Kabupaten Probolinggo',
                'satuan' => 'paket',
                'harga_wajar_min' => 35000,
                'harga_wajar_max' => 45000,
                'keterangan' => 'Konsumsi Rapat (Ref: Perbup 5/2025)',
            ],
        ];

        foreach ($sshData as $data) {
            if ($data['komponen_belanja_id']) {
                DB::table('master_ssh')->insert(array_merge($data, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
