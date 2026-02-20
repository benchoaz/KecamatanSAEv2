<?php

namespace App\Http\Controllers;

use App\Models\PembangunanDokumenSpj;
use App\Services\SpjTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SpjTemplateController extends Controller
{
    protected $templateService;

    public function __construct(SpjTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Mengunduh draft dokumen SPJ.
     */
    public function downloadDraft($id)
    {
        $spjDoc = PembangunanDokumenSpj::with(['pembangunanDesa.desa', 'masterDokumen'])->findOrFail($id);

        $metadata = $this->templateService->generateDraftMetadata($spjDoc);
        $preview = $this->templateService->getDraftPreview($spjDoc);

        // Untuk demo, kita return sebagai file teks .txt yang mensimulasikan konten terisi
        $filename = str_replace('.pdf', '.txt', $metadata['filename']);

        return Response::make($preview, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Menghapus baris dokumen SPJ (hanya jika status masih pending/draft).
     */
    public function destroy($id)
    {
        $spjDoc = PembangunanDokumenSpj::findOrFail($id);

        if ($spjDoc->status !== 'pending') {
            return response()->json(['message' => 'Dokumen yang sudah diunggah/diverifikasi tidak dapat dihapus.'], 422);
        }

        $spjDoc->delete();

        return response()->json(['message' => 'Draft dokumen SPJ berhasil dihapus.']);
    }
}
