<?php

namespace App\Helpers;

class SaeHelper
{
    /**
     * Determine required documents based on activity type and components.
     * 
     * @param string $jenis
     * @param array|null $components
     * @return array
     */
    public static function getChecklist($jenis, $components = [])
    {
        $checklist = [];
        $components = $components ?? [];

        // Logic based on Components
        if (in_array('Honor', $components)) {
            $checklist[] = [
                'title' => 'Tanda Terima Honor Narasumber/Upah',
                'description' => 'Bukti penyerahan uang honorarium atau upah kerja.',
                'template' => 'tanda-terima-honor'
            ];
        }

        if (in_array('Uang Saku', $components)) {
            $checklist[] = [
                'title' => 'Tanda Terima Uang Saku Peserta',
                'description' => 'Daftar penerimaan uang saku per orang.',
                'template' => 'tanda-terima-saku'
            ];
        }

        if (in_array('Mamin', $components)) {
            $checklist[] = [
                'title' => 'Kwitansi Konsumsi (Makan/Minum)',
                'description' => 'Nota atau kwitansi dari penyedia katering/toko.',
                'template' => 'kwitansi-umum'
            ];
        }

        if (in_array('ATK', $components)) {
            $checklist[] = [
                'title' => 'Kwitansi Alat Tulis Kantor (ATK)',
                'description' => 'Nota pembelian alat tulis atau kelengkapan kantor.',
                'template' => 'kwitansi-umum'
            ];
        }

        if (in_array('Banner', $components)) {
            $checklist[] = [
                'title' => 'Kwitansi Spanduk / Banner',
                'description' => 'Bukti pembayaran percetakan atribut kegiatan.',
                'template' => 'kwitansi-umum'
            ];
        }

        if (in_array('Material', $components)) {
            $checklist[] = [
                'title' => 'Nota/Kwitansi Pembelian Material',
                'description' => 'Bukti belanja bahan bangunan atau material fisik.',
                'template' => 'kwitansi-umum'
            ];
        }

        // Logic based on Tipo (Jenis Kegiatan)
        if ($jenis === 'Musdes') {
            $checklist[] = [
                'title' => 'Berita Acara Hasil Musyawarah Desa',
                'description' => 'Dokumen resmi kesepakatan hasil musyawarah.',
                'template' => 'berita-acara-musdes'
            ];
            $checklist[] = [
                'title' => 'Notulen Rapat',
                'description' => 'Catatan jalannya diskusi dan interaksi rapat.',
                'template' => 'notulen'
            ];
        }

        if ($jenis === 'Penyaluran BLT') {
            $checklist[] = [
                'title' => 'Daftar Penerima Manfaat (KPM)',
                'description' => 'Data detail warga penerima bantuan BLT.',
                'template' => 'daftar-penerima-blt'
            ];
            $checklist[] = [
                'title' => 'Tanda Terima Dana BLT',
                'description' => 'Bukti penyerahan uang tunai kepada warga.',
                'template' => 'tanda-terima-blt'
            ];
        }

        // Always required for accountability
        if (in_array('Uang Saku', $components) || in_array('Honor', $components) || $jenis === 'Musdes' || $jenis === 'Non Fisik' || $jenis === 'Penyaluran BLT') {
            $checklist[] = [
                'title' => 'Daftar Hadir Peserta/Undangan',
                'description' => 'Absensi kehadiran orang yang terlibat.',
                'template' => 'daftar-hadir'
            ];
        }

        $checklist[] = [
            'title' => 'Dokumentasi Foto Kegiatan',
            'description' => 'Foto proses pelaksanaan (Awal, Tengah, Akhir).',
            'template' => null // Usually just upload slot
        ];

        return $checklist;
    }

    /**
     * Get tax information based on component.
     */
    public static function getTaxInfo($component)
    {
        $taxRules = [
            'Honor' => 'Umumnya dikenakan PPh Pasal 21 (Honorarium). Silakan sesuaikan dengan tarif NPWP/Non-NPWP.',
            'Mamin' => 'Umumnya dikenakan Pajak Restoran (PB1) atau PPh Pasal 23 tergantung nilai dan penyedia.',
            'Material' => 'Perhatikan PPN 11% jika belanja di PKP dan PPh Pasal 22 untuk pengadaan barang.',
            'Banner' => 'Dikenakan PPN jika di percetakan PKP, dan Pajak Reklame jika dipasang di area publik.',
        ];

        return $taxRules[$component] ?? null;
    }
}
