<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villages = [
            ['kode' => '35.13.13.2001', 'nama' => 'Bago'],
            ['kode' => '35.13.13.2002', 'nama' => 'Kecik'],
            ['kode' => '35.13.13.2003', 'nama' => 'Alas Nyiur'],
            ['kode' => '35.13.13.2004', 'nama' => 'Sindet Lami'],
            ['kode' => '35.13.13.2005', 'nama' => 'Jambangan'],
            ['kode' => '35.13.13.2006', 'nama' => 'Klampokan'],
            ['kode' => '35.13.13.2007', 'nama' => 'Matekan'],
            ['kode' => '35.13.13.2008', 'nama' => 'Krampilan'],
            ['kode' => '35.13.13.2009', 'nama' => 'Besuk Agung'],
            ['kode' => '35.13.13.2010', 'nama' => 'Besuk Kidul'],
            ['kode' => '35.13.13.2011', 'nama' => 'Sumur Dalam'],
            ['kode' => '35.13.13.2012', 'nama' => 'Sindet Anyar'],
            ['kode' => '35.13.13.2013', 'nama' => 'Randu Jalak'],
            ['kode' => '35.13.13.2014', 'nama' => 'Alas Tengah'],
            ['kode' => '35.13.13.2015', 'nama' => 'Alas Kandang'],
            ['kode' => '35.13.13.2016', 'nama' => 'Alas Sumur Lor'],
            ['kode' => '35.13.13.2017', 'nama' => 'Sumberan'],
        ];

        foreach ($villages as $v) {
            \App\Models\Desa::updateOrCreate(
                ['kode_desa' => $v['kode']],
                [
                    'nama_desa' => $v['nama'],
                    'kecamatan' => 'Besuk',
                    'kabupaten' => 'Probolinggo',
                    'status' => 'aktif',
                    'is_active' => true
                ]
            );
        }
    }
}
