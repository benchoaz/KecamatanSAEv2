<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Desa;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(10);
        return view('kecamatan.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $desa = Desa::all();
        return view('kecamatan.announcements.create', compact('desa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'target_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'display_mode' => 'required|string',
            'priority' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'target_type' => $request->target_type,
            'target_desa_ids' => $request->target_type == 'specific_desa' ? $request->target_desa_ids : null,
            'display_mode' => $request->display_mode,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('kecamatan.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $desa = Desa::all();
        return view('kecamatan.announcements.edit', compact('announcement', 'desa'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'target_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'display_mode' => 'required|string',
            'priority' => 'required|string',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->input('content'),
            'target_type' => $request->target_type,
            'target_desa_ids' => $request->target_type == 'specific_desa' ? $request->target_desa_ids : null,
            'display_mode' => $request->display_mode,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('kecamatan.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        // Actually soft delete or archive as per objective? 
        // Objective says "No permanent deletion (use archive / nonactive)".
        // I'll just set it to inactive instead of deleting or use a soft delete if available.
        // Let's stick to setting is_active = false for "Arsip".
        $announcement->update(['is_active' => false]);
        return redirect()->route('kecamatan.announcements.index')->with('success', 'Pengumuman telah diarsipkan.');
    }
}
