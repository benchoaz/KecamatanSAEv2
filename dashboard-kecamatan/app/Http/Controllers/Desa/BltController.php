<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\BltDesa;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BltController extends Controller
{
    public function index()
    {
        $blt = BltDesa::where('desa_id', auth()->user()->desa_id)
            ->latest()
            ->get();

        return view('desa.blt.index', compact('blt'));
    }

    public function create()
    {
        return view('desa.blt.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_anggaran' => 'required|integer',
            'jumlah_kpm' => 'required|integer',
            'kpm_terealisasi' => 'required|integer',
            'total_dana_tersalurkan' => 'required|numeric',
            'status_penyaluran' => 'required|string',
            'dokumen_ba' => 'nullable|file|mimes:pdf|max:5120',
            'foto_penyaluran' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $validated;
        $data['desa_id'] = auth()->user()->desa_id;
        $data['status_laporan'] = 'Draft';

        if ($request->hasFile('dokumen_ba')) {
            $data['dokumen_ba'] = $request->file('dokumen_ba')->store('blt/ba', 'public');
        }

        if ($request->hasFile('daftar_kpm_file')) {
            $data['daftar_kpm_file'] = $request->file('daftar_kpm_file')->store('blt/data', 'public');
        }

        if ($request->hasFile('foto_penyaluran')) {
            $data['foto_penyaluran'] = $request->file('foto_penyaluran')->store('blt/foto', 'public');
        }

        $blt = BltDesa::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'create',
            'table_name' => 'blt_desa',
            'record_id' => $blt->id,
            'new_values' => $blt->toArray(),
            'domain' => 'desa'
        ]);

        return redirect()->route('desa.blt.index')->with('success', 'Data BLT berhasil disimpan sebagai draft.');
    }

    public function edit($id)
    {
        $item = BltDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);

        if ($item->status_laporan !== 'Draft' && $item->status_laporan !== 'Dikembalikan') {
            return back()->with('error', 'Laporan yang sudah dikirim tidak dapat diedit.');
        }

        return view('desa.blt.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = BltDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);

        $validated = $request->validate([
            'tahun_anggaran' => 'required|integer',
            'jumlah_kpm' => 'required|integer',
            'kpm_terealisasi' => 'required|integer',
            'total_dana_tersalurkan' => 'required|numeric',
            'status_penyaluran' => 'required|string',
            'dokumen_ba' => 'nullable|file|mimes:pdf|max:5120',
            'foto_penyaluran' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $oldValues = $item->toArray();
        $data = $validated;

        if ($request->hasFile('dokumen_ba')) {
            if ($item->dokumen_ba)
                Storage::disk('public')->delete($item->dokumen_ba);
            $data['dokumen_ba'] = $request->file('dokumen_ba')->store('blt/ba', 'public');
        }

        if ($request->hasFile('foto_penyaluran')) {
            if ($item->foto_penyaluran)
                Storage::disk('public')->delete($item->foto_penyaluran);
            $data['foto_penyaluran'] = $request->file('foto_penyaluran')->store('blt/foto', 'public');
        }

        $item->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'update',
            'table_name' => 'blt_desa',
            'record_id' => $item->id,
            'old_values' => $oldValues,
            'new_values' => $item->fresh()->toArray(),
            'domain' => 'desa'
        ]);

        return back()->with('success', 'Data BLT berhasil diperbarui.');
    }

    public function show($id)
    {
        $item = BltDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);
        return view('desa.blt.show', compact('item'));
    }

    public function submit($id)
    {
        $item = BltDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);

        if ($item->status_laporan !== 'Draft' && $item->status_laporan !== 'Dikembalikan') {
            return back()->with('error', 'Laporan sudah dikirim atau dicatat.');
        }

        $item->update(['status_laporan' => 'Dikirim']);

        return back()->with('success', 'Laporan BLT berhasil dikirim ke Kecamatan.');
    }
}
