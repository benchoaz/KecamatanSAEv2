<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Menu;
use App\Models\Aspek;
use App\Models\Indikator;
use Illuminate\Http\Request;

class KesraController extends Controller
{
    /**
     * Display a listing of Kesra submissions for the village
     */
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403, 'Anda tidak memiliki akses ke desa.');

        $desa_id = $user->desa_id;
        $desa = $user->desa;

        // Get Kesra menu
        $menu = Menu::where('kode_menu', 'kesra')->first();

        if (!$menu) {
            return redirect()->route('desa.dashboard')->with('error', 'Menu Kesra belum dikonfigurasi.');
        }

        // Get all Kesra submissions for this village
        $submissions = Submission::where('desa_id', $desa_id)
            ->where('menu_id', $menu->id)
            ->with(['aspek', 'verifikasi'])
            ->latest()
            ->paginate(15);

        // Get Kesra aspects for category breakdown
        $aspeks = Aspek::where('menu_id', $menu->id)->get();

        return view('desa.kesra.index', compact('submissions', 'menu', 'aspeks', 'desa'));
    }

    /**
     * Show the form for creating a new Kesra submission
     */
    public function create()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $menu = Menu::where('kode_menu', 'kesra')->first();

        if (!$menu) {
            return redirect()->route('desa.kesra.index')->with('error', 'Menu Kesra belum dikonfigurasi.');
        }

        // Get aspects and indicators
        $aspeks = Aspek::where('menu_id', $menu->id)->with('indikators')->get();

        return view('desa.kesra.create', compact('menu', 'aspeks'));
    }

    /**
     * Store a newly created Kesra submission in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $validated = $request->validate([
            'aspek_id' => 'required|exists:aspek,id',
            'kategori_kesra' => 'required|string|in:bansos,pendidikan,kesehatan,sosial_budaya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'data_pendukung' => 'nullable|array',
        ]);

        $menu = Menu::where('kode_menu', 'kesra')->firstOrFail();

        $submission = Submission::create([
            'desa_id' => $user->desa_id,
            'menu_id' => $menu->id,
            'aspek_id' => $validated['aspek_id'],
            'status' => 'draft',
            'periode' => now()->format('Y-m'),
        ]);

        // Store category and details in submission metadata or custom fields
        // You may need to add these fields to your submissions table or use a JSON field

        return redirect()->route('desa.kesra.show', $submission->id)
            ->with('success', 'Data Kesra berhasil disimpan sebagai draft.');
    }

    /**
     * Display the specified Kesra submission
     */
    public function show($id)
    {
        $user = auth()->user();
        $submission = Submission::with(['desa', 'menu', 'aspek', 'jawabanIndikator', 'buktiDukung', 'verifikasi'])
            ->findOrFail($id);

        // Security check
        abort_unless($submission->desa_id === $user->desa_id, 403);

        return view('desa.kesra.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified Kesra submission
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

        return view('desa.kesra.edit', compact('submission', 'menu', 'aspeks'));
    }

    /**
     * Update the specified Kesra submission in storage
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
            'kategori_kesra' => 'required|string|in:bansos,pendidikan,kesehatan,sosial_budaya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $submission->update([
            'aspek_id' => $validated['aspek_id'],
        ]);

        return redirect()->route('desa.kesra.show', $submission->id)
            ->with('success', 'Data Kesra berhasil diperbarui.');
    }

    /**
     * Submit the Kesra data for verification
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

        return redirect()->route('desa.kesra.index')
            ->with('success', 'Data Kesra berhasil dikirim untuk verifikasi Kecamatan.');
    }
}
