<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\PersonilDesa;
use App\Models\LembagaDesa;
use App\Models\DokumenDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function personil($id)
    {
        $personil = PersonilDesa::findOrFail($id);
        // Add permission check if needed (e.g. verify desa belongs to kecamatan)

        if (!$personil->file_sk || !Storage::disk('local')->exists($personil->file_sk)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $personil->file_sk));
    }

    public function personilFoto($id)
    {
        $personil = PersonilDesa::findOrFail($id);

        if (!$personil->foto || !Storage::disk('local')->exists($personil->foto)) {
            abort(404, 'Foto not found.');
        }

        return response()->file(storage_path('app/' . $personil->foto));
    }

    public function lembaga($id)
    {
        $lembaga = LembagaDesa::findOrFail($id);

        if (!$lembaga->file_sk || !Storage::disk('local')->exists($lembaga->file_sk)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $lembaga->file_sk));
    }

    public function dokumen($id)
    {
        $dokumen = DokumenDesa::findOrFail($id);

        if (!$dokumen->file_path || !Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $dokumen->file_path));
    }

    public function perencanaanBa($id)
    {
        $perencanaan = \App\Models\PerencanaanDesa::findOrFail($id);

        if (!$perencanaan->file_ba || !Storage::disk('local')->exists($perencanaan->file_ba)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $perencanaan->file_ba));
    }

    public function perencanaanAbsensi($id)
    {
        $perencanaan = \App\Models\PerencanaanDesa::findOrFail($id);

        if (!$perencanaan->file_absensi || !Storage::disk('local')->exists($perencanaan->file_absensi)) {
            abort(404, 'File not found.');
        }

        return response()->file(storage_path('app/' . $perencanaan->file_absensi));
    }

    public function perencanaanFoto($id)
    {
        $perencanaan = \App\Models\PerencanaanDesa::findOrFail($id);

        if (!$perencanaan->file_foto || !Storage::disk('local')->exists($perencanaan->file_foto)) {
            abort(404, 'Foto not found.');
        }

        return response()->file(storage_path('app/' . $perencanaan->file_foto));
    }
}
