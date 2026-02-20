<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;

class DesaMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = Desa::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_desa', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_desa', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $villages = $query->orderBy('kode_desa', 'asc')->paginate(15);

        $stats = [
            'total' => Desa::count(),
            'active' => Desa::where('status', 'aktif')->count(),
            'inactive' => Desa::where('status', 'tidak_aktif')->count(),
        ];

        return view('kecamatan.master.desa.index', compact('villages', 'stats'));
    }

    public function create()
    {
        return view('kecamatan.master.desa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_desa' => 'required|string|unique:desa,kode_desa|max:20',
            'nama_desa' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'status' => 'required|in:aktif,tidak_aktif',
            'website' => 'nullable|string|max:255',
        ]);

        if ($validated['website'] && !filter_var($validated['website'], FILTER_VALIDATE_URL)) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $validated['website'])) {
                $validated['website'] = "http://" . $validated['website'];
            }
        }

        Desa::create($validated);

        return redirect()->route('kecamatan.master.desa.index')
            ->with('success', 'Desa baru berhasil ditambahkan ke dalam sistem.');
    }

    public function edit(Desa $desa)
    {
        return view('kecamatan.master.desa.edit', compact('desa'));
    }

    public function update(Request $request, Desa $desa)
    {
        $validated = $request->validate([
            'kode_desa' => 'required|string|max:20|unique:desa,kode_desa,' . $desa->id,
            'nama_desa' => 'required|string|max:100',
            'status' => 'required|in:aktif,tidak_aktif',
            'alamat_kantor' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:255',
        ]);

        if ($validated['website'] && !filter_var($validated['website'], FILTER_VALIDATE_URL)) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $validated['website'])) {
                $validated['website'] = "http://" . $validated['website'];
            }
        }

        $desa->update($validated);

        return redirect()->route('kecamatan.master.desa.index')
            ->with('success', 'Data desa berhasil diperbarui.');
    }

    public function destroy(Desa $desa)
    {
        // Absolute rule: Logical deactivation only
        $desa->update(['status' => 'tidak_aktif', 'is_active' => false]);

        return back()->with('success', 'Status desa telah dinonaktifkan.');
    }
}