<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaDemografiSeeder extends Seeder
{
    public function run(): void
    {
        // Data demografi awal untuk 17 desa di Kecamatan Besuk
        // Data ini adalah estimasi awal - dapat diperbarui melalui panel admin
        $data = [
            ['nama_desa' => 'Alas Kandang',   'jumlah_penduduk' => 1850, 'jumlah_laki_laki' => 920, 'jumlah_perempuan' => 930, 'jumlah_kk' => 512, 'luas_wilayah' => 142.5, 'jumlah_rt' => 8,  'jumlah_rw' => 3],
            ['nama_desa' => 'Alas Nyiur',     'jumlah_penduduk' => 2340, 'jumlah_laki_laki' => 1180, 'jumlah_perempuan' => 1160, 'jumlah_kk' => 670, 'luas_wilayah' => 198.3, 'jumlah_rt' => 10, 'jumlah_rw' => 4],
            ['nama_desa' => 'Alas Sumur Lor', 'jumlah_penduduk' => 1620, 'jumlah_laki_laki' => 810, 'jumlah_perempuan' => 810, 'jumlah_kk' => 450, 'luas_wilayah' => 120.7, 'jumlah_rt' => 7,  'jumlah_rw' => 3],
            ['nama_desa' => 'Alas Tengah',    'jumlah_penduduk' => 2180, 'jumlah_laki_laki' => 1090, 'jumlah_perempuan' => 1090, 'jumlah_kk' => 610, 'luas_wilayah' => 175.2, 'jumlah_rt' => 9,  'jumlah_rw' => 3],
            ['nama_desa' => 'Bago',           'jumlah_penduduk' => 3250, 'jumlah_laki_laki' => 1620, 'jumlah_perempuan' => 1630, 'jumlah_kk' => 890, 'luas_wilayah' => 265.8, 'jumlah_rt' => 14, 'jumlah_rw' => 5],
            ['nama_desa' => 'Besuk Agung',    'jumlah_penduduk' => 4120, 'jumlah_laki_laki' => 2060, 'jumlah_perempuan' => 2060, 'jumlah_kk' => 1150, 'luas_wilayah' => 312.4, 'jumlah_rt' => 18, 'jumlah_rw' => 6],
            ['nama_desa' => 'Besuk Kidul',    'jumlah_penduduk' => 2870, 'jumlah_laki_laki' => 1430, 'jumlah_perempuan' => 1440, 'jumlah_kk' => 792, 'luas_wilayah' => 230.1, 'jumlah_rt' => 12, 'jumlah_rw' => 4],
            ['nama_desa' => 'Jambangan',      'jumlah_penduduk' => 1980, 'jumlah_laki_laki' => 985, 'jumlah_perempuan' => 995, 'jumlah_kk' => 550, 'luas_wilayah' => 156.9, 'jumlah_rt' => 8,  'jumlah_rw' => 3],
            ['nama_desa' => 'Kecik',          'jumlah_penduduk' => 2560, 'jumlah_laki_laki' => 1275, 'jumlah_perempuan' => 1285, 'jumlah_kk' => 712, 'luas_wilayah' => 210.5, 'jumlah_rt' => 11, 'jumlah_rw' => 4],
            ['nama_desa' => 'Klampokan',      'jumlah_penduduk' => 1750, 'jumlah_laki_laki' => 870, 'jumlah_perempuan' => 880, 'jumlah_kk' => 490, 'luas_wilayah' => 138.6, 'jumlah_rt' => 7,  'jumlah_rw' => 3],
            ['nama_desa' => 'Krampilan',      'jumlah_penduduk' => 2290, 'jumlah_laki_laki' => 1140, 'jumlah_perempuan' => 1150, 'jumlah_kk' => 635, 'luas_wilayah' => 182.4, 'jumlah_rt' => 10, 'jumlah_rw' => 3],
            ['nama_desa' => 'Matekan',        'jumlah_penduduk' => 3680, 'jumlah_laki_laki' => 1840, 'jumlah_perempuan' => 1840, 'jumlah_kk' => 1020, 'luas_wilayah' => 285.3, 'jumlah_rt' => 16, 'jumlah_rw' => 5],
            ['nama_desa' => 'Randu Jalak',    'jumlah_penduduk' => 2100, 'jumlah_laki_laki' => 1050, 'jumlah_perempuan' => 1050, 'jumlah_kk' => 582, 'luas_wilayah' => 168.7, 'jumlah_rt' => 9,  'jumlah_rw' => 3],
            ['nama_desa' => 'Sindet Anyar',   'jumlah_penduduk' => 1440, 'jumlah_laki_laki' => 715, 'jumlah_perempuan' => 725, 'jumlah_kk' => 400, 'luas_wilayah' => 115.2, 'jumlah_rt' => 6,  'jumlah_rw' => 2],
            ['nama_desa' => 'Sindet Lami',    'jumlah_penduduk' => 1680, 'jumlah_laki_laki' => 835, 'jumlah_perempuan' => 845, 'jumlah_kk' => 468, 'luas_wilayah' => 130.4, 'jumlah_rt' => 7,  'jumlah_rw' => 3],
            ['nama_desa' => 'Sumberan',       'jumlah_penduduk' => 2420, 'jumlah_laki_laki' => 1210, 'jumlah_perempuan' => 1210, 'jumlah_kk' => 674, 'luas_wilayah' => 192.8, 'jumlah_rt' => 10, 'jumlah_rw' => 4],
            ['nama_desa' => 'Sumur Dalam',    'jumlah_penduduk' => 1920, 'jumlah_laki_laki' => 955, 'jumlah_perempuan' => 965, 'jumlah_kk' => 535, 'luas_wilayah' => 150.6, 'jumlah_rt' => 8,  'jumlah_rw' => 3],
        ];

        foreach ($data as $row) {
            DB::table('desa')
                ->where('nama_desa', $row['nama_desa'])
                ->update([
                    'jumlah_penduduk'  => $row['jumlah_penduduk'],
                    'jumlah_laki_laki' => $row['jumlah_laki_laki'],
                    'jumlah_perempuan' => $row['jumlah_perempuan'],
                    'jumlah_kk'        => $row['jumlah_kk'],
                    'luas_wilayah'     => $row['luas_wilayah'],
                    'jumlah_rt'        => $row['jumlah_rt'],
                    'jumlah_rw'        => $row['jumlah_rw'],
                ]);
        }
    }
}
