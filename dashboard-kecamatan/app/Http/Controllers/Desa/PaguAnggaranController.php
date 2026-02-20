<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\DesaPaguAnggaran;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PaguAnggaranController extends Controller
{
    public function index()
    {
        $desa_id = auth()->user()->desa_id;
        $tahun = request('tahun', date('Y'));

        $pagus = DesaPaguAnggaran::where('desa_id', $desa_id)
            ->where('tahun', $tahun)
            ->get();

        return view('desa.pagu.index', compact('pagus', 'tahun'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'sumber_dana' => 'required|string',
            'jumlah_pagu' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $data = $validated;
        $data['desa_id'] = auth()->user()->desa_id;

        $pagu = DesaPaguAnggaran::updateOrCreate(
            [
                'desa_id' => $data['desa_id'],
                'tahun' => $data['tahun'],
                'sumber_dana' => $data['sumber_dana'],
            ],
            [
                'jumlah_pagu' => $data['jumlah_pagu'],
                'keterangan' => $data['keterangan'],
            ]
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'update_or_create',
            'table_name' => 'desa_pagu_anggaran',
            'record_id' => $pagu->id,
            'new_values' => $pagu->toArray(),
            'domain' => 'desa'
        ]);

        return back()->with('success', 'Anggaran awal (Pagu) berhasil disimpan.');
    }
}
