<?php

namespace App\Http\Controllers\Pemerintahan;

use App\Http\Controllers\Controller;
use App\Models\AparaturDesa;
use App\Models\AparaturDocument;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AparaturController extends Controller
{
    public function index(Request $request)
    {
        $query = AparaturDesa::with('desa');

        if ($request->filled('desa_id')) {
            $query->where('desa_id', $request->desa_id);
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('status')) {
            $query->where('status_jabatan', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        $aparatur = $query->latest()->paginate(10);
        $villages = Desa::where('is_active', true)->get();

        // Statistics for dashboard cards
        $stats = [
            'total_kades' => AparaturDesa::where('jabatan', 'Kades')->where('status_jabatan', 'Aktif')->count(),
            'total_perangkat' => AparaturDesa::where('jabatan', '!=', 'Kades')->where('status_jabatan', 'Aktif')->count(),
            'unverified' => AparaturDesa::where('status_verifikasi', AparaturDesa::VERIFIKASI_BELUM)->count(),
            'needs_revision' => AparaturDesa::where('status_verifikasi', AparaturDesa::VERIFIKASI_REVISI)->count(),
        ];

        return view('kecamatan.pemerintahan.aparatur.index', compact('aparatur', 'villages', 'stats'));
    }

    public function create()
    {
        $villages = Desa::where('is_active', true)->get();
        return view('kecamatan.pemerintahan.aparatur.create', compact('villages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'required|string|size:16|unique:aparatur_desa,nik',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan' => 'required|string',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after:tanggal_mulai',
            'status_jabatan' => 'required',
            'pendidikan_terakhir' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $aparatur = AparaturDesa::create(array_merge($validated, [
            'updated_by' => auth()->id(),
            'status_verifikasi' => AparaturDesa::VERIFIKASI_BELUM
        ]));

        if ($request->hasFile('dokumen_sk')) {
            $file = $request->file('dokumen_sk');
            $path = $file->store('aparatur/documents', 'public');

            AparaturDocument::create([
                'aparatur_desa_id' => $aparatur->id,
                'document_type' => 'SK_PENGANGKATAN',
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'is_active' => true
            ]);
        }

        return redirect()->route('kecamatan.pemerintahan.aparatur.index')
            ->with('success', 'Data aparatur desa berhasil ditambahkan.');
    }

    public function show(AparaturDesa $aparatur)
    {
        $aparatur->load(['desa', 'documents', 'updater']);
        return view('kecamatan.pemerintahan.aparatur.show', compact('aparatur'));
    }

    public function edit(AparaturDesa $aparatur)
    {
        $villages = Desa::where('is_active', true)->get();
        return view('kecamatan.pemerintahan.aparatur.edit', compact('aparatur', 'villages'));
    }

    public function update(Request $request, AparaturDesa $aparatur)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'required|string|size:16|unique:aparatur_desa,nik,' . $aparatur->id,
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan' => 'required|string',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after:tanggal_mulai',
            'status_jabatan' => 'required',
            'pendidikan_terakhir' => 'nullable|string',
            'dokumen_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $aparatur->update(array_merge($validated, [
            'updated_by' => auth()->id()
        ]));

        if ($request->hasFile('dokumen_sk')) {
            // Deactivate old documents of the same type if necessary
            AparaturDocument::where('aparatur_desa_id', $aparatur->id)
                ->where('document_type', 'SK_PENGANGKATAN')
                ->update(['is_active' => false]);

            $file = $request->file('dokumen_sk');
            $path = $file->store('aparatur/documents', 'public');

            AparaturDocument::create([
                'aparatur_desa_id' => $aparatur->id,
                'document_type' => 'SK_PENGANGKATAN',
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'is_active' => true
            ]);
        }

        return redirect()->route('kecamatan.pemerintahan.aparatur.index')
            ->with('success', 'Data aparatur desa berhasil diperbarui.');
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:' . AparaturDesa::VERIFIKASI_SUDAH . ',' . AparaturDesa::VERIFIKASI_REVISI,
            'catatan_kecamatan' => 'nullable|string',
        ]);

        $aparatur = AparaturDesa::findOrFail($id);
        $aparatur->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_kecamatan' => $request->catatan_kecamatan,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy(AparaturDesa $aparatur)
    {
        $aparatur->delete();
        return redirect()->route('kecamatan.pemerintahan.aparatur.index')
            ->with('success', 'Data aparatur desa berhasil dihapus.');
    }
}