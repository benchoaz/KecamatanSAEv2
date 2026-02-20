<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Helper to get Aspek ID
        $getAspekId = function ($kode) {
            return DB::table('aspek')->where('kode_aspek', $kode)->value('id');
        };

        $indikators = [
            // Pemerintahan - Perencanaan (pem_perencanaan)
            [
                'aspek_id' => $getAspekId('pem_perencanaan'),
                'kode_indikator' => 'ind_rpjmdes',
                'nama_indikator' => 'Dokumen RPJMDes',
                'deskripsi' => 'Ketersediaan dokumen RPJMDes yang masih berlaku',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('pem_perencanaan'),
                'kode_indikator' => 'ind_rkpdes',
                'nama_indikator' => 'Dokumen RKPDes Tahun Berjalan',
                'deskripsi' => 'Ketersediaan dokumen RKPDes untuk tahun anggaran berjalan',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('pem_perencanaan'),
                'kode_indikator' => 'ind_musdes_rkp',
                'nama_indikator' => 'Pelaksanaan Musdes RKPDes',
                'deskripsi' => 'Bukti pelaksanaan Musdes penyusunan RKPDes (BA, Absensi, Foto)',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Kegiatan',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('pem_perencanaan'),
                'kode_indikator' => 'ind_keselarasan',
                'nama_indikator' => 'Keselarasan dengan RKPD',
                'deskripsi' => 'Tingkat keselarasan program desa dengan prioritas daerah',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Sangat Selaras', 'Cukup Selaras', 'Kurang Selaras']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 4,
                'is_active' => true
            ],

            // Ekbang - Kepatuhan (ekb_kepatuhan)
            [
                'aspek_id' => $getAspekId('ekb_kepatuhan'),
                'kode_indikator' => 'ind_apbdes_waktu',
                'nama_indikator' => 'Penetapan APBDes Tepat Waktu',
                'deskripsi' => 'Tanggal penetapan Perdes APBDes (Maksimal 31 Desember)',
                'tipe_input' => 'date',
                'opsi_select' => null,
                'satuan' => 'Tanggal',
                'wajib_bukti' => true, // Bukti SK/Perdes
                'bobot' => 15.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('ekb_kepatuhan'),
                'kode_indikator' => 'ind_publikasi_apbdes',
                'nama_indikator' => 'Publikasi APBDes',
                'deskripsi' => 'Bukti publikasi APBDes (Banner/Baliho/Web)',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Kesra - Pendidikan (kes_pendidikan)
            [
                'aspek_id' => $getAspekId('kes_pendidikan'),
                'kode_indikator' => 'ind_ats',
                'nama_indikator' => 'Data Siswa Putus Sekolah (ATS)',
                'deskripsi' => 'Jumlah anak tidak sekolah (ATS) di wilayah desa',
                'tipe_input' => 'number',
                'opsi_select' => null,
                'satuan' => 'Anak',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('kes_pendidikan'),
                'kode_indikator' => 'ind_sarpras_pend',
                'nama_indikator' => 'Kondisi Sarana Prasarana Pendidikan',
                'deskripsi' => 'Evaluasi kondisi fisik gedung sekolah (PAUD/TK/SD)',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Sangat Baik', 'Baik', 'Cukup', 'Kurang']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Kesra - Kesehatan (kes_kesehatan)
            [
                'aspek_id' => $getAspekId('kes_kesehatan'),
                'kode_indikator' => 'ind_stunting',
                'nama_indikator' => 'Jumlah Balita Stunting',
                'deskripsi' => 'Data balita dengan status stunting berdasarkan hasil posyandu',
                'tipe_input' => 'number',
                'opsi_select' => null,
                'satuan' => 'Balita',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('kes_kesehatan'),
                'kode_indikator' => 'ind_bumil_resti',
                'nama_indikator' => 'Data Ibu Hamil Resiko Tinggi',
                'deskripsi' => 'Jumlah ibu hamil yang masuk kategori resiko tinggi',
                'tipe_input' => 'number',
                'opsi_select' => null,
                'satuan' => 'Orang',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Kesra - Posyandu (kes_posyandu)
            [
                'aspek_id' => $getAspekId('kes_posyandu'),
                'kode_indikator' => 'ind_kinerja_posyandu',
                'nama_indikator' => 'Kinerja Posyandu',
                'deskripsi' => 'Tingkat keaktifan dan layanan posyandu di desa',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Aktif', 'Cukup Aktif', 'Kurang Aktif']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('kes_posyandu'),
                'kode_indikator' => 'ind_alkes_posyandu',
                'nama_indikator' => 'Ketersediaan Alat Kesehatan Posyandu',
                'deskripsi' => 'Kelengkapan alat kesehatan dasar di posyandu',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Lengkap', 'Cukup', 'Kurang']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Kesra - Sosial (kes_social)
            [
                'aspek_id' => $getAspekId('kes_sosial'),
                'kode_indikator' => 'ind_blt_dd',
                'nama_indikator' => 'Data Penerima BLT-DD',
                'deskripsi' => 'Daftar keluarga penerima manfaat BLT Dana Desa',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('kes_sosial'),
                'kode_indikator' => 'ind_dtks_update',
                'nama_indikator' => 'Status Update DTKS',
                'deskripsi' => 'Progres pemutakhiran data terpadu kesejahteraan sosial',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Sudah Update', 'Sedang Proses', 'Belum Update']),
                'satuan' => 'Status',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Trantibum - Linmas (tran_linmas)
            [
                'aspek_id' => $getAspekId('tran_linmas'),
                'kode_indikator' => 'ind_linmas_data',
                'nama_indikator' => 'Data Anggota Satlinmas',
                'deskripsi' => 'Daftar anggota Satlinmas yang aktif sesuai SK',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('tran_linmas'),
                'kode_indikator' => 'ind_poskamling_kondisi',
                'nama_indikator' => 'Kondisi Poskamling',
                'deskripsi' => 'Tingkat kelayakan sarana poskamling di wilayah desa',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Sangat Layak', 'Layak', 'Kurang Layak']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Trantibum - Bencana (tran_bencana)
            [
                'aspek_id' => $getAspekId('tran_bencana'),
                'kode_indikator' => 'ind_rawan_bencana',
                'nama_indikator' => 'Peta Rawan Bencana',
                'deskripsi' => 'Ketersediaan data/peta wilayah rawan bencana desa',
                'tipe_input' => 'file',
                'opsi_select' => null,
                'satuan' => 'Dokumen',
                'wajib_bukti' => true,
                'bobot' => 10.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('tran_bencana'),
                'kode_indikator' => 'ind_logistik_bencana',
                'nama_indikator' => 'Ketersediaan Logistik Bencana',
                'deskripsi' => 'Ketersediaan stok logistik darurat di tingkat desa',
                'tipe_input' => 'select',
                'opsi_select' => json_encode(['Tersedia', 'Terbatas', 'Kosong']),
                'satuan' => 'Status',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],

            // Trantibum - Konflik (tran_konflik)
            [
                'aspek_id' => $getAspekId('tran_konflik'),
                'kode_indikator' => 'ind_gangguan_kamtibmas',
                'nama_indikator' => 'Laporan Gangguan Kamtibmas',
                'deskripsi' => 'Jumlah laporan gangguan ketertiban selama periode pelaporan',
                'tipe_input' => 'number',
                'opsi_select' => null,
                'satuan' => 'Kejadian',
                'wajib_bukti' => false,
                'bobot' => 5.00,
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'aspek_id' => $getAspekId('tran_konflik'),
                'kode_indikator' => 'ind_konflik_social',
                'nama_indikator' => 'Kejadian Konflik Sosial',
                'deskripsi' => 'Jumlah dan detail kejadian konflik sosial di masyarakat',
                'tipe_input' => 'number',
                'opsi_select' => null,
                'satuan' => 'Kejadian',
                'wajib_bukti' => true,
                'bobot' => 5.00,
                'urutan' => 2,
                'is_active' => true
            ],
        ];

        foreach ($indikators as $ind) {
            if ($ind['aspek_id']) {
                DB::table('indikator')->updateOrInsert(
                    ['kode_indikator' => $ind['kode_indikator']],
                    $ind
                );
            }
        }
    }
}
