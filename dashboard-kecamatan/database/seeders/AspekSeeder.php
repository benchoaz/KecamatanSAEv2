<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AspekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Helper to get menu ID
        $getMenuId = function ($kode) {
            return DB::table('menu')->where('kode_menu', $kode)->value('id');
        };

        $aspeks = [
            // Pemerintahan
            [
                'menu_id' => $getMenuId('pemerintahan'),
                'kode_aspek' => 'pem_perencanaan',
                'nama_aspek' => 'Perencanaan Desa & Kecamatan',
                'deskripsi' => 'Musdes, Musrenbang, dan dokumen perencanaan',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('pemerintahan'),
                'kode_aspek' => 'pem_struktur',
                'nama_aspek' => 'Struktur Pemerintahan Desa',
                'deskripsi' => 'Data perangkat, SK, dan masa jabatan',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('pemerintahan'),
                'kode_aspek' => 'pem_siltap',
                'nama_aspek' => 'SILTAP Perangkat Desa',
                'deskripsi' => 'Penghasilan tetap dan tunjangan',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('pemerintahan'),
                'kode_aspek' => 'pem_aset',
                'nama_aspek' => 'Inventaris & Aset Desa',
                'deskripsi' => 'Inventaris barang dan tanah kas desa',
                'urutan' => 4,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('pemerintahan'),
                'kode_aspek' => 'pem_admin',
                'nama_aspek' => 'Administrasi & Pelaporan',
                'deskripsi' => 'Tertib administrasi dan laporan rutin',
                'urutan' => 5,
                'is_active' => true
            ],

            // Ekbang
            [
                'menu_id' => $getMenuId('ekbang'),
                'kode_aspek' => 'ekb_kepatuhan',
                'nama_aspek' => 'Kepatuhan Desa',
                'deskripsi' => 'Kepatuhan terhadap regulasi APBDes',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('ekbang'),
                'kode_aspek' => 'ekb_monev',
                'nama_aspek' => 'Monev Dana Desa',
                'deskripsi' => 'Monitoring dan evaluasi penyerapan DD',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('ekbang'),
                'kode_aspek' => 'ekb_realisasi',
                'nama_aspek' => 'Realisasi APBDes',
                'deskripsi' => 'Laporan realisasi anggaran',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('ekbang'),
                'kode_aspek' => 'ekb_fisik',
                'nama_aspek' => 'Fisik Pembangunan',
                'deskripsi' => 'Progress fisik proyek pembangunan',
                'urutan' => 4,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('ekbang'),
                'kode_aspek' => 'ekb_audit',
                'nama_aspek' => 'Akuntabilitas & Audit',
                'deskripsi' => 'Tindak lanjut temuan pemeriksaan',
                'urutan' => 5,
                'is_active' => true
            ],

            // Kesra
            [
                'menu_id' => $getMenuId('kesra'),
                'kode_aspek' => 'kes_pendidikan',
                'nama_aspek' => 'Data Pendidikan',
                'deskripsi' => 'Data sekolah, siswa, dan ATS',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('kesra'),
                'kode_aspek' => 'kes_kesehatan',
                'nama_aspek' => 'Kesehatan & Stunting',
                'deskripsi' => 'Kesehatan ibu anak dan stunting',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('kesra'),
                'kode_aspek' => 'kes_posyandu',
                'nama_aspek' => 'Posyandu & Layanan Dasar',
                'deskripsi' => 'Kinerja Posyandu',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('kesra'),
                'kode_aspek' => 'kes_sosial',
                'nama_aspek' => 'Data Sosial & Kemiskinan',
                'deskripsi' => 'Data DTKS dan bantuan sosial',
                'urutan' => 4,
                'is_active' => true
            ],

            // Trantibum
            [
                'menu_id' => $getMenuId('trantibum'),
                'kode_aspek' => 'tran_linmas',
                'nama_aspek' => 'Satlinmas & Poskamling',
                'deskripsi' => 'Data anggota Linmas dan Poskamling',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('trantibum'),
                'kode_aspek' => 'tran_bencana',
                'nama_aspek' => 'Kebencanaan',
                'deskripsi' => 'Data rawan bencana dan kejadian',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('trantibum'),
                'kode_aspek' => 'tran_konflik',
                'nama_aspek' => 'Trantib & Konflik Sosial',
                'deskripsi' => 'Gangguan ketertiban dan konflik',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'menu_id' => $getMenuId('trantibum'),
                'kode_aspek' => 'tran_tagana',
                'nama_aspek' => 'Data TAGANA Desa',
                'deskripsi' => 'Kontak person Taruna Siaga Bencana tiap desa',
                'urutan' => 4,
                'is_active' => true
            ],
        ];

        foreach ($aspeks as $a) {
            if ($a['menu_id']) { // Only insert if menu exists
                DB::table('aspek')->updateOrInsert(
                    ['kode_aspek' => $a['kode_aspek']],
                    $a
                );
            }
        }
    }
}
