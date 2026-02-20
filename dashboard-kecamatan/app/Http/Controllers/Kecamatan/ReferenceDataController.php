<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\MasterSsh;
use App\Models\MasterSbu;
use App\Models\MasterKomponenBelanja;
use Illuminate\Http\Request;

class ReferenceDataController extends Controller
{
    /**
     * SSH Index
     */
    public function sshIndex()
    {
        $ssh = MasterSsh::with('komponenBelanja')->latest()->paginate(20);
        $komponens = MasterKomponenBelanja::active()->orderBy('nama_komponen')->get();
        return view('kecamatan.referensi.ssh', compact('ssh', 'komponens'));
    }

    /**
     * SSH Store
     */
    public function sshStore(Request $request)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_wajar_min' => 'required|numeric|min:0',
            'harga_wajar_max' => 'required|numeric|min:0|gte:harga_wajar_min',
            'komponen_belanja_id' => 'required|exists:master_komponen_belanja,id',
            'tahun' => 'required|integer|min:2020|max:2030',
            'wilayah' => 'nullable|string|max:255',
        ]);

        MasterSsh::create($validated);
        return back()->with('success', 'Data SSH berhasil ditambahkan.');
    }

    /**
     * SSH Update
     */
    public function sshUpdate(Request $request, $id)
    {
        $ssh = MasterSsh::findOrFail($id);
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_wajar_min' => 'required|numeric|min:0',
            'harga_wajar_max' => 'required|numeric|min:0|gte:harga_wajar_min',
            'komponen_belanja_id' => 'required|exists:master_komponen_belanja,id',
            'tahun' => 'required|integer|min:2020|max:2030',
            'wilayah' => 'nullable|string|max:255',
        ]);

        $ssh->update($validated);
        return back()->with('success', 'Data SSH berhasil diperbarui.');
    }

    /**
     * SSH Destroy
     */
    public function sshDestroy($id)
    {
        MasterSsh::findOrFail($id)->delete();
        return back()->with('success', 'Data SSH berhasil dihapus.');
    }

    /**
     * SBU Index
     */
    public function sbuIndex()
    {
        $sbu = MasterSbu::with('komponenBelanja')->latest()->paginate(20);
        $komponens = MasterKomponenBelanja::active()->orderBy('nama_komponen')->get();
        return view('kecamatan.referensi.sbu', compact('sbu', 'komponens'));
    }

    /**
     * SBU Store
     */
    public function sbuStore(Request $request)
    {
        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'batas_maks' => 'required|numeric|min:0',
            'komponen_belanja_id' => 'required|exists:master_komponen_belanja,id',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);

        MasterSbu::create($validated);
        return back()->with('success', 'Data SBU berhasil ditambahkan.');
    }

    /**
     * SBU Update
     */
    public function sbuUpdate(Request $request, $id)
    {
        $sbu = MasterSbu::findOrFail($id);
        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'batas_maks' => 'required|numeric|min:0',
            'komponen_belanja_id' => 'required|exists:master_komponen_belanja,id',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);

        $sbu->update($validated);
        return back()->with('success', 'Data SBU berhasil diperbarui.');
    }

    /**
     * SBU Destroy
     */
    public function sbuDestroy($id)
    {
        MasterSbu::findOrFail($id)->delete();
        return back()->with('success', 'Data SBU berhasil dihapus.');
    }
}
