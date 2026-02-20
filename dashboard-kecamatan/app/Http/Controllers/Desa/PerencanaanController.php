<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\PerencanaanDesa;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PerencanaanController extends Controller
{
    public function index()
    {
        $desa_id = auth()->user()->desa_id;
        $perencanaan = PerencanaanDesa::where('desa_id', $desa_id)
            ->orderBy('tahun', 'desc')
            ->orderBy('tipe_dokumen', 'asc')
            ->get();

        return view('desa.perencanaan.index', compact('perencanaan', 'desa_id'));
    }

    public function create(Request $request)
    {
        $year = $request->get('tahun', date('Y'));
        $mode = $this->getModeByYear($year);

        return view('desa.perencanaan.create', compact('year', 'mode'));
    }

    public function store(Request $request)
    {
        $year = $request->tahun;
        $mode = $this->getModeByYear($year);

        $rules = [
            'tipe_dokumen' => 'required|in:RPJMDes,RKPDes,APBDes',
            'tahun' => 'required|integer',
            'file_dokumen' => 'required|file|mimes:pdf|max:10240',
        ];

        if ($mode === PerencanaanDesa::MODE_TRANSISI || $mode === PerencanaanDesa::MODE_TERSTRUKTUR) {
            $rules['nomor_perdes'] = 'required|string';
            $rules['tanggal_perdes'] = 'required|date';
        }

        if ($mode === PerencanaanDesa::MODE_TERSTRUKTUR) {
            // Additional rules for terstructured can be added here
            // e.g., requiring parent relation
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $file_path = $request->file('file_dokumen')->store('perencanaan/' . $year, 'local');

            $perencanaan = PerencanaanDesa::create([
                'desa_id' => auth()->user()->desa_id,
                'tipe_dokumen' => $validated['tipe_dokumen'],
                'tahun' => $validated['tahun'],
                'mode_input' => $mode,
                'nomor_perdes' => $validated['nomor_perdes'] ?? null,
                'tanggal_perdes' => $validated['tanggal_perdes'] ?? null,
                'file_ba' => $file_path, // Mapping file to existing column or using a new one if available
                'status' => PerencanaanDesa::STATUS_DRAFT,
            ]);

            // Audit Log
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'table_name' => 'perencanaan_desa',
                'record_id' => $perencanaan->id,
                'new_values' => json_encode(['tipe_dokumen' => $validated['tipe_dokumen'], 'tahun' => $year, 'mode' => $mode]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'domain' => 'Perencanaan',
            ]);

            DB::commit();
            return redirect()->route('desa.perencanaan.index')->with('success', 'Dokumen perencanaan berhasil disimpan sebagai draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $perencanaan = PerencanaanDesa::findOrFail($id);
        abort_unless($perencanaan->desa_id === auth()->user()->desa_id, 403);

        return view('desa.perencanaan.show', compact('perencanaan'));
    }

    public function submit($id)
    {
        $perencanaan = PerencanaanDesa::findOrFail($id);
        abort_unless($perencanaan->desa_id === auth()->user()->desa_id, 403);
        abort_unless($perencanaan->status === PerencanaanDesa::STATUS_DRAFT, 400);

        $perencanaan->update(['status' => PerencanaanDesa::STATUS_DIKIRIM]);

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'submit',
            'table_name' => 'perencanaan_desa',
            'record_id' => $perencanaan->id,
            'old_values' => json_encode(['status' => 'draft']),
            'new_values' => json_encode(['status' => 'dikirim']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'domain' => 'Perencanaan',
        ]);

        return back()->with('success', 'Dokumen berhasil dikirim ke Kecamatan.');
    }

    private function getModeByYear($year)
    {
        if ($year <= 2025)
            return PerencanaanDesa::MODE_ARSIP;
        if ($year == 2026)
            return PerencanaanDesa::MODE_TRANSISI;
        return PerencanaanDesa::MODE_TERSTRUKTUR;
    }
}
