<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\TrantibumKejadian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrantibumKejadianController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $kejadians = TrantibumKejadian::where('desa_id', $user->desa_id)
            ->latest()
            ->paginate(10);

        return view('desa.trantibum.kejadian.index', compact('kejadians'));
    }

    public function create()
    {
        return view('desa.trantibum.kejadian.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'jenis_kejadian' => 'required|string|max:255',
            'waktu_kejadian' => 'required|date',
            'lokasi_koordinat' => 'nullable|string|max:255',
            'lokasi_deskripsi' => 'nullable|string',
            'kronologi' => 'required|string',
            'dampak_kerusakan' => 'nullable|string',
            'kondisi_mutakhir' => 'nullable|string',
            'upaya_penanganan' => 'nullable|string',
            'pihak_terlibat' => 'nullable|string',
            'foto_kejadian' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['desa_id'] = $user->desa_id;

        if ($request->hasFile('foto_kejadian')) {
            $path = $request->file('foto_kejadian')->store('trantibum/kejadian', 'public');
            $data['foto_kejadian'] = $path;
        }

        TrantibumKejadian::create($data);

        return redirect()->route('desa.trantibum.kejadian.index')
            ->with('success', 'Laporan kejadian berhasil dikirim ke Kecamatan.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $kejadian = TrantibumKejadian::where('desa_id', $user->desa_id)->findOrFail($id);

        return view('desa.trantibum.kejadian.show', compact('kejadian'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $kejadian = TrantibumKejadian::where('desa_id', $user->desa_id)->findOrFail($id);

        if ($kejadian->foto_kejadian) {
            Storage::disk('public')->delete($kejadian->foto_kejadian);
        }

        $kejadian->delete();

        return redirect()->route('desa.trantibum.kejadian.index')
            ->with('success', 'Laporan kejadian berhasil dihapus.');
    }
}
