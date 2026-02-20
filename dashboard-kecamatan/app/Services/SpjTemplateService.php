<?php

namespace App\Services;

use App\Models\PembangunanDokumenSpj;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpjTemplateService
{
    /**
     * Menghasilkan draft dokumen SPJ dengan data yang sudah terisi.
     * Untuk saat ini, kita akan melakukan simulasi string replacement pada file teks/markdown
     * atau mengembalikan metadata untuk diproses oleh library PDF/Word nantinya.
     * 
     * @param PembangunanDokumenSpj $spjDoc
     * @return array
     */
    public function generateDraftMetadata(PembangunanDokumenSpj $spjDoc)
    {
        $pembangunan = $spjDoc->pembangunanDesa;
        $masterDoc = $spjDoc->masterDokumen;
        $desa = $pembangunan->desa;

        // Data yang akan di-inject ke template
        $data = [
            'NAMA_DESA' => strtoupper($desa->nama_desa),
            'KECAMATAN' => 'BESUK', // Hardcoded for now based on project context
            'NAMA_KEGIATAN' => $pembangunan->nama_kegiatan,
            'LOKASI' => $pembangunan->lokasi,
            'TAHUN_ANGGARAN' => $pembangunan->tahun_anggaran,
            'PAGU_ANGGARAN' => number_format($pembangunan->pagu_anggaran, 0, ',', '.'),
            'SUMBER_DANA' => $pembangunan->sumber_dana,
            'TANGGAL_DRAFT' => now()->translatedFormat('d F Y'),
        ];

        return [
            'doc_name' => $masterDoc->nama_dokumen,
            'template_file' => $masterDoc->file_template,
            'injected_data' => $data,
            'filename' => Str::slug($masterDoc->nama_dokumen . '_' . $pembangunan->nama_kegiatan) . '.pdf'
        ];
    }

    /**
     * Simulasi konten draft (untuk demo)
     */
    public function getDraftPreview(PembangunanDokumenSpj $spjDoc)
    {
        $metadata = $this->generateDraftMetadata($spjDoc);
        $data = $metadata['injected_data'];

        $content = "DRAFT DOKUMEN: {$metadata['doc_name']}\n";
        $content .= "========================================\n\n";
        $content .= "PEMERINTAH KABUPATEN PROBOLINGGO\n";
        $content .= "KECAMATAN {$data['KECAMATAN']}\n";
        $content .= "DESA {$data['NAMA_DESA']}\n\n";
        $content .= "----------------------------------------\n";
        $content .= "Kegiatan: {$data['NAMA_KEGIATAN']}\n";
        $content .= "Lokasi  : {$data['LOKASI']}\n";
        $content .= "Anggaran: Rp {$data['PAGU_ANGGARAN']}\n";
        $content .= "Sumber  : {$data['SUMBER_DANA']}\n";
        $content .= "----------------------------------------\n\n";
        $content .= "Isi dokumen ini dihasilkan secara otomatis oleh Sistem Kecamatan SAE sebagai draft pendukung administrasi desa.\n\n";
        $content .= "Dicetak pada: {$data['TANGGAL_DRAFT']}\n";

        return $content;
    }
}
