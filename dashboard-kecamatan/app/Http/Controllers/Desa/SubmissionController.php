<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Desa\DesaSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->latest()
            ->paginate(10);

        return view('desa.submissions.index', compact('submissions'));
    }

    public function create()
    {
        return view('desa.submissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'modul' => 'required|string|max:50',
            'periode' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $submission = DesaSubmission::create([
                'desa_id' => auth()->user()->desa_id,
                'judul' => $validated['judul'],
                'modul' => $validated['modul'],
                'periode' => $validated['periode'],
                'status' => DesaSubmission::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('desa.submissions.edit', $submission->id)
                ->with('success', 'Draft laporan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat draft: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403, 'Laporan ini sudah dikunci dan tidak dapat diedit.');
        }

        return view('desa.submissions.edit', compact('submission'));
    }

    public function submit(Request $request, $id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403, 'Laporan tidak dapat dikirim.');
        }

        // Simple validation: Ensure title is present or any other mandatory checks
        if (empty($submission->judul)) {
            return back()->with('error', 'Judul laporan wajib diisi.');
        }

        $submission->update([
            'status' => DesaSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        // Audit Log logic would go here (e.g., AuditLog::create(...))

        return redirect()->route('desa.submissions.index')
            ->with('success', 'Laporan berhasil dikirim ke Kecamatan.');
    }

    // Additional generic update method for saving draft details
    public function update(Request $request, $id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403, 'Laporan tidak dapat diedit.');
        }

        $submission->update($request->only(['judul', 'periode']));

        return back()->with('success', 'Perubahan disimpan.');
    }
}
