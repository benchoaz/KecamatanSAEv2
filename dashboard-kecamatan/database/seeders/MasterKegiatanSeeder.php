<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKegiatanSeeder extends Seeder
{
    /**
     * Seed Master Bidang, Sub Bidang, dan Kegiatan berdasarkan struktur APBDes standar.
     */
    public function run(): void
    {
        // ==================== 5 BIDANG APBDes ====================
        $bidangs = [
            ['kode_bidang' => '01', 'nama_bidang' => 'Penyelenggaraan Pemerintahan Desa', 'urutan' => 1],
            ['kode_bidang' => '02', 'nama_bidang' => 'Pelaksanaan Pembangunan Desa', 'urutan' => 2],
            ['kode_bidang' => '03', 'nama_bidang' => 'Pembinaan Kemasyarakatan Desa', 'urutan' => 3],
            ['kode_bidang' => '04', 'nama_bidang' => 'Pemberdayaan Masyarakat Desa', 'urutan' => 4],
            ['kode_bidang' => '05', 'nama_bidang' => 'Penanggulangan Bencana, Darurat & Mendesak', 'urutan' => 5],
        ];

        foreach ($bidangs as $bidang) {
            DB::table('master_bidang')->updateOrInsert(
                ['kode_bidang' => $bidang['kode_bidang']],
                array_merge($bidang, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // ==================== SUB BIDANG ====================
        $subBidangs = [
            // Bidang 01 - Pemerintahan
            ['bidang' => '01', 'kode' => '01', 'nama' => 'Penyelenggaraan Belanja Penghasilan Tetap, Tunjangan dan Operasional Pemerintahan Desa'],
            ['bidang' => '01', 'kode' => '02', 'nama' => 'Sarana dan Prasarana Pemerintahan Desa'],
            ['bidang' => '01', 'kode' => '03', 'nama' => 'Administrasi Kependudukan, Pencatatan Sipil, Statistik dan Kearsipan'],
            ['bidang' => '01', 'kode' => '04', 'nama' => 'Tata Praja Pemerintahan, Perencanaan, Keuangan dan Pelaporan'],
            ['bidang' => '01', 'kode' => '05', 'nama' => 'Pertanahan'],

            // Bidang 02 - Pembangunan
            ['bidang' => '02', 'kode' => '01', 'nama' => 'Pendidikan'],
            ['bidang' => '02', 'kode' => '02', 'nama' => 'Kesehatan'],
            ['bidang' => '02', 'kode' => '03', 'nama' => 'Pekerjaan Umum dan Penataan Ruang'],
            ['bidang' => '02', 'kode' => '04', 'nama' => 'Kawasan Permukiman'],
            ['bidang' => '02', 'kode' => '05', 'nama' => 'Kehutanan dan Lingkungan Hidup'],
            ['bidang' => '02', 'kode' => '06', 'nama' => 'Perhubungan, Komunikasi dan Informatika'],
            ['bidang' => '02', 'kode' => '07', 'nama' => 'Energi dan Sumber Daya Mineral'],
            ['bidang' => '02', 'kode' => '08', 'nama' => 'Pariwisata'],

            // Bidang 03 - Pembinaan Kemasyarakatan
            ['bidang' => '03', 'kode' => '01', 'nama' => 'Ketenteraman, Ketertiban Umum dan Perlindungan Masyarakat'],
            ['bidang' => '03', 'kode' => '02', 'nama' => 'Kebudayaan dan Keagamaan'],
            ['bidang' => '03', 'kode' => '03', 'nama' => 'Kepemudaan dan Olah Raga'],
            ['bidang' => '03', 'kode' => '04', 'nama' => 'Kelembagaan Masyarakat'],

            // Bidang 04 - Pemberdayaan
            ['bidang' => '04', 'kode' => '01', 'nama' => 'Kelautan dan Perikanan'],
            ['bidang' => '04', 'kode' => '02', 'nama' => 'Pertanian dan Peternakan'],
            ['bidang' => '04', 'kode' => '03', 'nama' => 'Peningkatan Kapasitas Aparatur Desa'],
            ['bidang' => '04', 'kode' => '04', 'nama' => 'Pemberdayaan Perempuan, Perlindungan Anak dan Keluarga'],
            ['bidang' => '04', 'kode' => '05', 'nama' => 'Koperasi, Usaha Mikro Kecil dan Menengah'],
            ['bidang' => '04', 'kode' => '06', 'nama' => 'Dukungan Penanaman Modal'],
            ['bidang' => '04', 'kode' => '07', 'nama' => 'Perdagangan dan Perindustrian'],

            // Bidang 05 - Bencana
            ['bidang' => '05', 'kode' => '01', 'nama' => 'Penanggulangan Bencana'],
            ['bidang' => '05', 'kode' => '02', 'nama' => 'Keadaan Darurat'],
            ['bidang' => '05', 'kode' => '03', 'nama' => 'Keadaan Mendesak'],
        ];

        foreach ($subBidangs as $index => $sub) {
            $bidangId = DB::table('master_bidang')->where('kode_bidang', $sub['bidang'])->value('id');
            if ($bidangId) {
                DB::table('master_sub_bidang')->updateOrInsert(
                    ['kode_sub_bidang' => $sub['bidang'] . '.' . $sub['kode']],
                    [
                        'bidang_id' => $bidangId,
                        'nama_sub_bidang' => $sub['nama'],
                        'urutan' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // ==================== KEGIATAN SAMPLE ====================
        $kegiatans = [
            // Fisik - Pembangunan (02.03)
            ['sub' => '02.03', 'kode' => '001', 'nama' => 'Pembangunan/Rehabilitasi/Peningkatan Jalan Desa', 'jenis' => 'fisik', 'satuan' => 'meter'],
            ['sub' => '02.03', 'kode' => '002', 'nama' => 'Pembangunan/Rehabilitasi/Peningkatan Jalan Lingkungan Permukiman', 'jenis' => 'fisik', 'satuan' => 'meter'],
            ['sub' => '02.03', 'kode' => '003', 'nama' => 'Pembangunan/Rehabilitasi/Peningkatan Drainase', 'jenis' => 'fisik', 'satuan' => 'meter'],
            ['sub' => '02.03', 'kode' => '004', 'nama' => 'Pembangunan/Rehabilitasi/Peningkatan Jembatan', 'jenis' => 'fisik', 'satuan' => 'unit'],
            ['sub' => '02.04', 'kode' => '001', 'nama' => 'Pembangunan/Rehabilitasi Sarana Prasarana Air Bersih', 'jenis' => 'fisik', 'satuan' => 'unit'],
            ['sub' => '02.04', 'kode' => '002', 'nama' => 'Pembangunan/Rehabilitasi Jamban Komunal', 'jenis' => 'fisik', 'satuan' => 'unit'],
            ['sub' => '02.02', 'kode' => '001', 'nama' => 'Pembangunan/Rehabilitasi Posyandu', 'jenis' => 'fisik', 'satuan' => 'unit'],

            // Non Fisik - Pelatihan (04.03)
            ['sub' => '04.03', 'kode' => '001', 'nama' => 'Pelatihan Kepala Desa dan Perangkat Desa', 'jenis' => 'non_fisik', 'satuan' => 'kegiatan'],
            ['sub' => '04.03', 'kode' => '002', 'nama' => 'Pendidikan dan Pelatihan BPD', 'jenis' => 'non_fisik', 'satuan' => 'kegiatan'],
            ['sub' => '04.04', 'kode' => '001', 'nama' => 'Pelatihan Kader Kesehatan Masyarakat', 'jenis' => 'non_fisik', 'satuan' => 'kegiatan'],
            ['sub' => '04.04', 'kode' => '002', 'nama' => 'Pelatihan Kader Pemberdayaan PKK', 'jenis' => 'non_fisik', 'satuan' => 'kegiatan'],

            // Musdes - Perencanaan (01.04)
            ['sub' => '01.04', 'kode' => '001', 'nama' => 'Penyelenggaraan Musyawarah Perencanaan Desa (Musrenbangdes)', 'jenis' => 'musdes', 'satuan' => 'kegiatan'],
            ['sub' => '01.04', 'kode' => '002', 'nama' => 'Penyelenggaraan Musyawarah Desa', 'jenis' => 'musdes', 'satuan' => 'kegiatan'],
            ['sub' => '01.04', 'kode' => '003', 'nama' => 'Penyusunan Dokumen RPJMDes', 'jenis' => 'musdes', 'satuan' => 'dokumen'],
            ['sub' => '01.04', 'kode' => '004', 'nama' => 'Penyusunan Dokumen RKPDes', 'jenis' => 'musdes', 'satuan' => 'dokumen'],

            // BLT
            ['sub' => '05.03', 'kode' => '001', 'nama' => 'Bantuan Langsung Tunai Dana Desa (BLT-DD)', 'jenis' => 'blt', 'satuan' => 'KPM'],
            ['sub' => '04.04', 'kode' => '003', 'nama' => 'Bantuan Insentif Kader Posyandu', 'jenis' => 'blt', 'satuan' => 'orang'],
            ['sub' => '04.04', 'kode' => '004', 'nama' => 'Bantuan Insentif Guru PAUD', 'jenis' => 'blt', 'satuan' => 'orang'],
        ];

        foreach ($kegiatans as $index => $keg) {
            $subBidangId = DB::table('master_sub_bidang')->where('kode_sub_bidang', $keg['sub'])->value('id');
            if ($subBidangId) {
                DB::table('master_kegiatan')->updateOrInsert(
                    ['kode_kegiatan' => $keg['sub'] . '.' . $keg['kode']],
                    [
                        'sub_bidang_id' => $subBidangId,
                        'nama_kegiatan' => $keg['nama'],
                        'jenis_kegiatan' => $keg['jenis'],
                        'satuan_default' => $keg['satuan'],
                        'urutan' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
