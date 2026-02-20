<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\TrantibumRelawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrantibumRelawanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $relawans = TrantibumRelawan::where('desa_id', $user->desa_id)
            ->latest()
            ->paginate(12);

        return view('desa.trantibum.relawan.index', compact('relawans'));
    }

    public function create()
    {
        return view('desa.trantibum.relawan.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
            'sk_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $validated;
        $data['desa_id'] = $user->desa_id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('trantibum/relawan/foto', 'public');
        }

        if ($request->hasFile('sk_file')) {
            $data['sk_file'] = $request->file('sk_file')->store('trantibum/relawan/sk', 'public');
        }

        TrantibumRelawan::create($data);

        return redirect()->route('desa.trantibum.relawan.index')
            ->with('success', 'Anggota relawan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $relawan = TrantibumRelawan::where('desa_id', $user->desa_id)->findOrFail($id);

        if ($relawan->foto) {
            Storage::disk('public')->delete($relawan->foto);
        }

        if ($relawan->sk_file) {
            Storage::disk('public')->delete($relawan->sk_file);
        }

        $relawan->delete();

        return redirect()->route('desa.trantibum.relawan.index')
            ->with('success', 'Anggota relawan berhasil dihapus.');
    }
}
