<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Menu;
use App\Models\Aspek;
use Illuminate\Http\Request;

class TrantibumController extends Controller
{
    /**
     * Display a listing of Trantibum submissions for the village
     */
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403, 'Anda tidak memiliki akses ke desa.');

        $desa_id = $user->desa_id;
        $desa = $user->desa;

        // Get Trantibum menu
        $menu = Menu::where('kode_menu', 'trantibum')->first();

        if (!$menu) {
            return redirect()->route('desa.dashboard')->with('error', 'Menu Trantibum belum dikonfigurasi.');
        }

        // Get all Trantibum submissions for this village
        $submissions = Submission::where('desa_id', $desa_id)
            ->where('menu_id', $menu->id)
            ->with(['aspek', 'verifikasi'])
            ->latest()
            ->paginate(15);

        return view('desa.trantibum.index', compact('submissions', 'menu', 'desa'));
    }

    /**
     * Show the form for creating a new Trantibum submission
     */
    public function create()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $menu = Menu::where('kode_menu', 'trantibum')->first();

        if (!$menu) {
            return redirect()->route('desa.trantibum.index')->with('error', 'Menu Trantibum belum dikonfigurasi.');
        }

        // Get aspects and indicators
        $aspeks = Aspek::where('menu_id', $menu->id)->with('indikators')->get();

        return view('desa.trantibum.create', compact('menu', 'aspeks'));
    }

    /**
     * Store a newly created Trantibum submission in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $validated = $request->validate([
            'aspek_id' => 'required|exists:aspek,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_kejadian' => 'nullable|date',
        ]);

        $menu = Menu::where('kode_menu', 'trantibum')->firstOrFail();

        $submission = Submission::create([
            'desa_id' => $user->desa_id,
            'menu_id' => $menu->id,
            'aspek_id' => $validated['aspek_id'],
            'status' => 'draft',
            'periode' => now()->format('Y-m'),
        ]);

        return redirect()->route('desa.trantibum.show', $submission->id)
            ->with('success', 'Data Trantibum berhasil disimpan sebagai draft.');
    }

    /**
     * Display the specified Trantibum submission
     */

    public function show($id)
    {
        $user = auth()->user();
        $submission = Submission::with(['desa', 'menu', 'aspek', 'jawabanIndikator', 'buktiDukung', 'verifikasi'])
            ->findOrFail($id);

        // Security check
        abort_unless($submission->desa_id === $user->desa_id, 403);

        return view('desa.trantibum.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified Trantibum submission
     */
    public function edit($id)
    {
        $user = auth()->user();
        $submission = Submission::with(['aspek', 'jawabanIndikator'])->findOrFail($id);

        // Security checks
        abort_unless($submission->desa_id === $user->desa_id, 403);
        abort_if($submission->status === 'submitted' || $submission->status === 'approved', 403, 'Data yang sudah disubmit tidak dapat diedit.');

        $menu = $submission->menu;
        $aspeks = Aspek::where('menu_id', $menu->id)->with('indikators')->get();

        return view('desa.trantibum.edit', compact('submission', 'menu', 'aspeks'));
    }

    /**
     * Update the specified Trantibum submission in storage
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $submission = Submission::findOrFail($id);

        // Security checks
        abort_unless($submission->desa_id === $user->desa_id, 403);
        abort_if($submission->status === 'submitted' || $submission->status === 'approved', 403);

        $validated = $request->validate([
            'aspek_id' => 'required|exists:aspek,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_kejadian' => 'nullable|date',
        ]);

        $submission->update([
            'aspek_id' => $validated['aspek_id'],
        ]);

        return redirect()->route('desa.trantibum.show', $submission->id)
            ->with('success', 'Data Trantibum berhasil diperbarui.');
    }

    /**
     * Submit the Trantibum data for verification
     */
    public function submit(Request $request, $id)
    {
        $user = auth()->user();
        $submission = Submission::findOrFail($id);

        // Security checks
        abort_unless($submission->desa_id === $user->desa_id, 403);
        abort_if($submission->status !== 'draft', 403, 'Hanya data dengan status draft yang dapat disubmit.');

        // Update status to submitted
        $submission->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('desa.trantibum.index')
            ->with('success', 'Data Trantibum berhasil dikirim untuk verifikasi Kecamatan.');
    }
}
