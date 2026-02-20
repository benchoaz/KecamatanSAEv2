<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\PembangunanDesa;
use App\Models\PembangunanLogbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembangunanLogbookController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $pembangunanId = $request->query('pembangunan_id');

        $pembangunan = PembangunanDesa::where('desa_id', $user->desa_id)
            ->findOrFail($pembangunanId);

        $logbooks = $pembangunan->logbooks()->latest()->get();

        return response()->json([
            'pembangunan' => $pembangunan,
            'logbooks' => $logbooks
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'pembangunan_desa_id' => 'required|exists:pembangunan_desa,id',
            'progres_fisik' => 'required|integer|min:0|max:100',
            'catatan' => 'required|string',
            'kendala' => 'nullable|string',
            'foto_progres' => 'required|image|max:2048',
        ]);

        // Security check
        $pembangunan = PembangunanDesa::where('desa_id', $user->desa_id)
            ->findOrFail($validated['pembangunan_desa_id']);

        $data = $validated;

        if ($request->hasFile('foto_progres')) {
            $path = $request->file('foto_progres')->store('pembangunan/logbooks', 'public');
            $data['foto_progres'] = $path;
        }

        $logbook = PembangunanLogbook::create($data);

        // Update main project progress
        $pembangunan->update([
            'progres_fisik' => $validated['progres_fisik'] . '%',
            // If progress is 100%, maybe update status? 
            // allowing manual status update for now along with this if needed, 
            // but for now just updating the physical progress field.
        ]);

        return back()->with('success', 'Logbook progres berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $logbook = PembangunanLogbook::with('pembangunan')->findOrFail($id);

        if ($logbook->pembangunan->desa_id !== $user->desa_id) {
            abort(403);
        }

        if ($logbook->foto_progres) {
            Storage::disk('public')->delete($logbook->foto_progres);
        }

        $logbook->delete();

        return back()->with('success', 'Logbook berhasil dihapus.');
    }
}
