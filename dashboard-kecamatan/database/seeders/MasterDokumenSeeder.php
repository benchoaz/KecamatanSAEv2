<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDokumenSeeder extends Seeder
{
    /**
     * Seed dokumen SPJ umum desa.
     */
    public function run(): void
    {
        $dokumens = [
            // Umum
            ['kode' => 'DOC.01', 'nama' => 'Daftar Hadir', 'kategori' => 'umum', 'desc' => 'Daftar hadir peserta rapat/kegiatan'],
            ['kode' => 'DOC.02', 'nama' => 'Notulen Rapat', 'kategori' => 'umum', 'desc' => 'Catatan hasil musyawarah/rapat'],
            ['kode' => 'DOC.03', 'nama' => 'Foto Dokumentasi', 'kategori' => 'umum', 'desc' => 'Foto bukti pelaksanaan kegiatan'],
            ['kode' => 'DOC.04', 'nama' => 'Undangan', 'kategori' => 'umum', 'desc' => 'Surat undangan pertemuan'],

            // Keuangan / Honor
            ['kode' => 'DOC.05', 'nama' => 'Kwitansi Pembayaran / Bukti Kas', 'kategori' => 'honor', 'desc' => 'Kwitansi tanda terima uang'],
            ['kode' => 'DOC.06', 'nama' => 'Tanda Terima Honor', 'kategori' => 'honor', 'desc' => 'Daftar tanda terima honor narasumber/panitia'],
            ['kode' => 'DOC.07', 'nama' => 'Tanda Terima Bantuan', 'kategori' => 'honor', 'desc' => 'Daftar tanda terima BLT atau bantuan lainnya'],

            // Fisik
            ['kode' => 'DOC.08', 'nama' => 'RAB (Rencana Anggaran Biaya)', 'kategori' => 'fisik', 'desc' => 'Rincian anggaran untuk pekerjaan fisik'],
            ['kode' => 'DOC.09', 'nama' => 'Gambar Rencana / Sketsa', 'kategori' => 'fisik', 'desc' => 'Gambar desain teknis pekerjaan fisik'],
            ['kode' => 'DOC.10', 'nama' => 'Laporan Progres Fisik (0%, 50%, 100%)', 'kategori' => 'fisik', 'desc' => 'Foto dan narasi kemajuan fisik'],
            ['kode' => 'DOC.11', 'nama' => 'Berita Acara Serah Terima (BAST)', 'kategori' => 'fisik', 'desc' => 'BA serah terima hasil pekerjaan'],

            // Perjalanan
            ['kode' => 'DOC.12', 'nama' => 'SPPD (Surat Perintah Perjalanan Dinas)', 'kategori' => 'perjalanan', 'desc' => 'Lembar SPPD yang sudah distempel'],
            ['kode' => 'DOC.13', 'nama' => 'Laporan Perjalanan Dinas', 'kategori' => 'perjalanan', 'desc' => 'Laporan hasil kunjungan/tugas'],

            // Musdes / Surat
            ['kode' => 'DOC.14', 'nama' => 'Berita Acara Musyawarah Desa', 'kategori' => 'surat', 'desc' => 'BA hasil musdes resmi'],
            ['kode' => 'DOC.15', 'nama' => 'SK Kepala Desa', 'kategori' => 'surat', 'desc' => 'Keputusan Kades terkait kegiatan'],
        ];

        foreach ($dokumens as $index => $doc) {
            DB::table('master_dokumen_spj')->updateOrInsert(
                ['kode_dokumen' => $doc['kode']],
                [
                    'nama_dokumen' => $doc['nama'],
                    'kategori' => $doc['kategori'],
                    'deskripsi' => $doc['desc'],
                    'urutan' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Link ke kegiatan (Logic Auto-Suggest Dokumen)
        $this->linkDokumenToKegiatan();
    }

    private function linkDokumenToKegiatan(): void
    {
        // Get master kegiatan IDs
        $kegiatanFisik = DB::table('master_kegiatan')->where('jenis_kegiatan', 'fisik')->pluck('id');
        $kegiatanNonFisik = DB::table('master_kegiatan')->where('jenis_kegiatan', 'non_fisik')->pluck('id');
        $kegiatanMusdes = DB::table('master_kegiatan')->where('jenis_kegiatan', 'musdes')->pluck('id');
        $kegiatanBlt = DB::table('master_kegiatan')->where('jenis_kegiatan', 'blt')->pluck('id');

        // Helper to get doc ID by kode
        $getDocId = fn($kode) => DB::table('master_dokumen_spj')->where('kode_dokumen', $kode)->value('id');

        // 1. Dokumen untuk Fisik
        $docFisik = ['DOC.08', 'DOC.09', 'DOC.10', 'DOC.11', 'DOC.03', 'DOC.05'];
        foreach ($kegiatanFisik as $kegId) {
            foreach ($docFisik as $kode) {
                $docId = $getDocId($kode);
                if ($docId) {
                    DB::table('kegiatan_dokumen_spj')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_dokumen_spj_id' => $docId],
                        ['is_wajib' => true, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // 2. Dokumen untuk Non Fisik (Pelatihan/Sosialisasi)
        $docNonFisik = ['DOC.01', 'DOC.02', 'DOC.03', 'DOC.04', 'DOC.05', 'DOC.06'];
        foreach ($kegiatanNonFisik as $kegId) {
            foreach ($docNonFisik as $kode) {
                $docId = $getDocId($kode);
                if ($docId) {
                    DB::table('kegiatan_dokumen_spj')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_dokumen_spj_id' => $docId],
                        ['is_wajib' => true, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // 3. Dokumen untuk Musdes
        $docMusdes = ['DOC.14', 'DOC.01', 'DOC.02', 'DOC.03', 'DOC.04'];
        foreach ($kegiatanMusdes as $kegId) {
            foreach ($docMusdes as $kode) {
                $docId = $getDocId($kode);
                if ($docId) {
                    DB::table('kegiatan_dokumen_spj')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_dokumen_spj_id' => $docId],
                        ['is_wajib' => true, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // 4. Dokumen untuk BLT
        $docBlt = ['DOC.07', 'DOC.15', 'DOC.03', 'DOC.05'];
        foreach ($kegiatanBlt as $kegId) {
            foreach ($docBlt as $kode) {
                $docId = $getDocId($kode);
                if ($docId) {
                    DB::table('kegiatan_dokumen_spj')->updateOrInsert(
                        ['master_kegiatan_id' => $kegId, 'master_dokumen_spj_id' => $docId],
                        ['is_wajib' => true, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
