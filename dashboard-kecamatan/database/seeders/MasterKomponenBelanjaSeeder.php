<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKomponenBelanjaSeeder extends Seeder
{
    /**
     * Seed komponen belanja umum yang dipakai desa.
     */
    public function run(): void
    {
        $komponens = [
            // Honor
            ['kode' => 'HON.01', 'nama' => 'Honor Narasumber', 'kategori' => 'honor', 'pajak' => true, 'satuan' => 'OJ'],
            ['kode' => 'HON.02', 'nama' => 'Honor Panitia', 'kategori' => 'honor', 'pajak' => true, 'satuan' => 'OK'],
            ['kode' => 'HON.03', 'nama' => 'Uang Saku Peserta', 'kategori' => 'honor', 'pajak' => false, 'satuan' => 'OH'],
            ['kode' => 'HON.04', 'nama' => 'Upah Tenaga Kerja', 'kategori' => 'honor', 'pajak' => false, 'satuan' => 'HOK'],
            ['kode' => 'HON.05', 'nama' => 'Upah Mandor', 'kategori' => 'honor', 'pajak' => false, 'satuan' => 'HOK'],

            // Konsumsi
            ['kode' => 'KON.01', 'nama' => 'Konsumsi Rapat', 'kategori' => 'konsumsi', 'pajak' => false, 'satuan' => 'paket'],
            ['kode' => 'KON.02', 'nama' => 'Snack / Kudapan', 'kategori' => 'konsumsi', 'pajak' => false, 'satuan' => 'dus'],
            ['kode' => 'KON.03', 'nama' => 'Makan Minum Kegiatan', 'kategori' => 'konsumsi', 'pajak' => false, 'satuan' => 'orang'],
            ['kode' => 'KON.04', 'nama' => 'Air Mineral', 'kategori' => 'konsumsi', 'pajak' => false, 'satuan' => 'dus'],

            // Barang
            ['kode' => 'BRG.01', 'nama' => 'ATK (Alat Tulis Kantor)', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'paket'],
            ['kode' => 'BRG.02', 'nama' => 'Spanduk / Banner', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'lembar'],
            ['kode' => 'BRG.03', 'nama' => 'Fotokopi / Penggandaan', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'lembar'],
            ['kode' => 'BRG.04', 'nama' => 'Sertifikat / Piagam', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'lembar'],
            ['kode' => 'BRG.05', 'nama' => 'Bahan Material Bangunan', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'paket'],
            ['kode' => 'BRG.06', 'nama' => 'Semen', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'sak'],
            ['kode' => 'BRG.07', 'nama' => 'Pasir', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'm3'],
            ['kode' => 'BRG.08', 'nama' => 'Batu Belah', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'm3'],
            ['kode' => 'BRG.09', 'nama' => 'Besi Beton', 'kategori' => 'barang', 'pajak' => false, 'satuan' => 'batang'],

            // Jasa
            ['kode' => 'JAS.01', 'nama' => 'Sewa Tempat / Gedung', 'kategori' => 'jasa', 'pajak' => true, 'satuan' => 'hari'],
            ['kode' => 'JAS.02', 'nama' => 'Sewa Sound System', 'kategori' => 'jasa', 'pajak' => true, 'satuan' => 'paket'],
            ['kode' => 'JAS.03', 'nama' => 'Jasa Dokumentasi', 'kategori' => 'jasa', 'pajak' => true, 'satuan' => 'kegiatan'],
            ['kode' => 'JAS.04', 'nama' => 'Sewa Kendaraan', 'kategori' => 'jasa', 'pajak' => true, 'satuan' => 'hari'],
            ['kode' => 'JAS.05', 'nama' => 'Jasa Konstruksi / Tukang', 'kategori' => 'jasa', 'pajak' => true, 'satuan' => 'HOK'],

            // Perjalanan
            ['kode' => 'PJL.01', 'nama' => 'Transport Lokal', 'kategori' => 'perjalanan', 'pajak' => false, 'satuan' => 'orang'],
            ['kode' => 'PJL.02', 'nama' => 'SPPD Dalam Kabupaten', 'kategori' => 'perjalanan', 'pajak' => false, 'satuan' => 'OH'],
            ['kode' => 'PJL.03', 'nama' => 'SPPD Luar Kabupaten', 'kategori' => 'perjalanan', 'pajak' => false, 'satuan' => 'OH'],
            ['kode' => 'PJL.04', 'nama' => 'Uang Harian Perjalanan', 'kategori' => 'perjalanan', 'pajak' => false, 'satuan' => 'OH'],
            ['kode' => 'PJL.05', 'nama' => 'Biaya Penginapan', 'kategori' => 'perjalanan', 'pajak' => false, 'satuan' => 'malam'],
        ];

        foreach ($komponens as $index => $komp) {
            DB::table('master_komponen_belanja')->updateOrInsert(
                ['kode_komponen' => $komp['kode']],
                [
                    'nama_komponen' => $komp['nama'],
                    'kategori' => $komp['kategori'],
                    'objek_pajak' => $komp['pajak'],
                    'satuan' => $komp['satuan'],
                    'urutan' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ==================== LINK KOMPONEN KE KEGIATAN ====================
        $this->linkKomponenToKegiatan();
    }

    /**
     * Link komponen belanja yang umum dipakai untuk setiap jenis kegiatan.
     */
    private function linkKomponenToKegiatan(): void
    {
        // Komponen untuk kegiatan FISIK
        $komponenFisik = ['HON.04', 'HON.05', 'BRG.05', 'BRG.06', 'BRG.07', 'BRG.08', 'BRG.09', 'JAS.05'];

        // Komponen untuk kegiatan NON FISIK (pelatihan, sosialisasi)
        $komponenNonFisik = ['HON.01', 'HON.02', 'HON.03', 'KON.01', 'KON.02', 'BRG.01', 'BRG.02', 'BRG.03', 'BRG.04'];

        // Komponen untuk kegiatan MUSDES
        $komponenMusdes = ['HON.02', 'KON.01', 'KON.02', 'BRG.01', 'BRG.02', 'BRG.03'];

        // Get kegiatan IDs by jenis
        $kegiatanFisik = DB::table('master_kegiatan')->where('jenis_kegiatan', 'fisik')->pluck('id');
        $kegiatanNonFisik = DB::table('master_kegiatan')->where('jenis_kegiatan', 'non_fisik')->pluck('id');
        $kegiatanMusdes = DB::table('master_kegiatan')->where('jenis_kegiatan', 'musdes')->pluck('id');

        // Link fisik
        foreach ($kegiatanFisik as $kegId) {
            foreach ($komponenFisik as $index => $kodeKomp) {
                $kompId = DB::table('master_komponen_belanja')->where('kode_komponen', $kodeKomp)->value('id');
                if ($kompId) {
                    DB::table('kegiatan_komponen_belanja')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_komponen_belanja_id' => $kompId],
                        ['is_wajib' => in_array($kodeKomp, ['HON.04', 'BRG.05']), 'urutan' => $index + 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // Link non fisik
        foreach ($kegiatanNonFisik as $kegId) {
            foreach ($komponenNonFisik as $index => $kodeKomp) {
                $kompId = DB::table('master_komponen_belanja')->where('kode_komponen', $kodeKomp)->value('id');
                if ($kompId) {
                    DB::table('kegiatan_komponen_belanja')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_komponen_belanja_id' => $kompId],
                        ['is_wajib' => in_array($kodeKomp, ['HON.01', 'KON.01', 'BRG.01']), 'urutan' => $index + 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // Link musdes
        foreach ($kegiatanMusdes as $kegId) {
            foreach ($komponenMusdes as $index => $kodeKomp) {
                $kompId = DB::table('master_komponen_belanja')->where('kode_komponen', $kodeKomp)->value('id');
                if ($kompId) {
                    DB::table('kegiatan_komponen_belanja')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_komponen_belanja_id' => $kompId],
                        ['is_wajib' => in_array($kodeKomp, ['KON.01']), 'urutan' => $index + 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
