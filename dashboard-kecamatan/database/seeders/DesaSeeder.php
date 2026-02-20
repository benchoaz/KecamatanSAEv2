<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desa = [
            ['kode_desa' => '3513062001', 'nama_desa' => 'Alas Kandang', 'is_active' => true, 'website' => 'https://alaskandang.tatadesa.com/'],
            ['kode_desa' => '3513062002', 'nama_desa' => 'Alas Nyiur', 'is_active' => true, 'website' => 'https://alasnyiur.tatadesa.com/'],
            ['kode_desa' => '3513062003', 'nama_desa' => 'Alas Sumur Lor', 'is_active' => true, 'website' => 'https://alassumurlor.tatadesa.com/'],
            ['kode_desa' => '3513062004', 'nama_desa' => 'Alas Tengah', 'is_active' => true, 'website' => 'https://alastengah.tatadesa.com/'],
            ['kode_desa' => '3513062005', 'nama_desa' => 'Bago', 'is_active' => true, 'website' => 'https://bago.tatadesa.com/'],
            ['kode_desa' => '3513062006', 'nama_desa' => 'Besuk Agung', 'is_active' => true, 'website' => 'https://besukagung.tatadesa.com/'],
            ['kode_desa' => '3513062007', 'nama_desa' => 'Besuk Kidul', 'is_active' => true, 'website' => 'https://besukkidul.tatadesa.com/'],
            ['kode_desa' => '3513062008', 'nama_desa' => 'Jambangan', 'is_active' => true, 'website' => 'https://jambangan.tatadesa.com/'],
            ['kode_desa' => '3513062009', 'nama_desa' => 'Kecik', 'is_active' => true, 'website' => 'https://kecik.tatadesa.com/'],
            ['kode_desa' => '3513062010', 'nama_desa' => 'Klampokan', 'is_active' => true, 'website' => 'https://klampokan.tatadesa.com/'],
            ['kode_desa' => '3513062011', 'nama_desa' => 'Krampilan', 'is_active' => true, 'website' => 'https://krampilan.tatadesa.com/'],
            ['kode_desa' => '3513062012', 'nama_desa' => 'Matekan', 'is_active' => true, 'website' => 'https://matekan.tatadesa.com/'],
            ['kode_desa' => '3513062013', 'nama_desa' => 'Randu Jalak', 'is_active' => true, 'website' => 'https://randujalak.tatadesa.com/'],
            ['kode_desa' => '3513062014', 'nama_desa' => 'Sindet Anyar', 'is_active' => true, 'website' => 'https://sindetanyar.tatadesa.com/'],
            ['kode_desa' => '3513062015', 'nama_desa' => 'Sindet Lami', 'is_active' => true, 'website' => 'https://sindetlami.tatadesa.com/'],
            ['kode_desa' => '3513062016', 'nama_desa' => 'Sumberan', 'is_active' => true, 'website' => 'https://sumberan.tatadesa.com/'],
            ['kode_desa' => '3513062017', 'nama_desa' => 'Sumurdalam', 'is_active' => true, 'website' => 'https://sumurdalam.tatadesa.com/'],
        ];

        foreach ($desa as $d) {
            DB::table('desa')->updateOrInsert(
                ['kode_desa' => $d['kode_desa']],
                $d
            );
        }
    }
}
