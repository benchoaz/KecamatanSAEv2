<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\PersonilDesa;
use App\Models\LembagaDesa;
use App\Models\DokumenDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Download SK Personil (Perangkat & BPD)
     */
    public function personil($id)
    {
        $personil = PersonilDesa::findOrFail($id);

        // Security Check: Ensure user belongs to the same desa
        if (auth()->user()->desa_id !== $personil->desa_id) {
            abort(403, 'Unauthorized access to this file.');
        }

        if (!$personil->file_sk || !Storage::disk('local')->exists($personil->file_sk)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $personil->file_sk));
    }

    /**
     * Download SK Lembaga
     */
    public function lembaga($id)
    {
        $lembaga = LembagaDesa::findOrFail($id);

        if (auth()->user()->desa_id !== $lembaga->desa_id) {
            abort(403, 'Unauthorized access to this file.');
        }

        if (!$lembaga->file_sk || !Storage::disk('local')->exists($lembaga->file_sk)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $lembaga->file_sk));
    }

    /**
     * Download Dokumen Desa
     */
    public function dokumen($id)
    {
        $dokumen = DokumenDesa::findOrFail($id);

        if (auth()->user()->desa_id !== $dokumen->desa_id) {
            abort(403, 'Unauthorized access to this file.');
        }

        if (!$dokumen->file_path || !Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $dokumen->file_path));
    }
}
