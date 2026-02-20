<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'kode_menu' => 'pemerintahan',
                'nama_menu' => 'Pemerintahan',
                'deskripsi' => 'Pengelolaan data aparatur, kelembagaan, dan administrasi kependudukan desa.',
                'icon' => 'fas fa-landmark',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'kode_menu' => 'ekbang',
                'nama_menu' => 'Ekonomi & Pembangunan',
                'deskripsi' => 'Monitoring pembangunan fisik, realisasi APBDes, dan pemberdayaan ekonomi masyarakat.',
                'icon' => 'fas fa-chart-line',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'kode_menu' => 'kesra',
                'nama_menu' => 'Kesejahteraan Rakyat',
                'deskripsi' => 'Pengelolaan bantuan sosial, kegiatan keagamaan, pendidikan, dan kesehatan masyarakat.',
                'icon' => 'fas fa-hand-holding-heart',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'kode_menu' => 'trantibum',
                'nama_menu' => 'Trantibum',
                'deskripsi' => 'Monitoring ketentraman, ketertiban umum, dan perlindungan masyarakat di tingkat wilayah.',
                'icon' => 'fas fa-shield-halved',
                'urutan' => 4,
                'is_active' => true
            ],
            [
                'kode_menu' => 'analisa',
                'nama_menu' => 'Analisa Data',
                'deskripsi' => 'Dashboard integrasi data untuk pemetaan potensi dan evaluasi kinerja pembangunan.',
                'icon' => 'fas fa-magnifying-glass-chart',
                'urutan' => 5,
                'is_active' => true
            ],
        ];

        foreach ($menus as $m) {
            DB::table('menu')->updateOrInsert(
                ['kode_menu' => $m['kode_menu']],
                $m
            );
        }
    }
}
