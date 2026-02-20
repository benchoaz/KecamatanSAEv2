<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    /**
     * List semua berita untuk dashboard internal.
     */
    public function index()
    {
        $berita = Berita::with('author')->latest()->paginate(10);
        return view('kecamatan.berita.index', compact('berita'));
    }

    public function create()
    {
        $this->authorize('create', Berita::class);
        return view('kecamatan.berita.create');
    }

    /**
     * Simpan berita baru.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Berita::class);
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'kategori' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('berita/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        $validated['author_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['judul']) . '-' . Str::random(5);

        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $berita = Berita::create($validated);

        $this->logAudit('create', $berita);

        return redirect()->route('kecamatan.berita.index')->with('success', 'Berita berhasil dibuat.');
    }

    /**
     * Form edit berita.
     */
    public function edit($id)
    {
        $berita = Berita::findOrFail($id);
        $this->authorize('update', $berita);
        return view('kecamatan.berita.edit', compact('berita'));
    }

    /**
     * Update berita.
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        $this->authorize('update', $berita);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'kategori' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($berita->thumbnail) {
                Storage::disk('public')->delete($berita->thumbnail);
            }
            $path = $request->file('thumbnail')->store('berita/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        if ($validated['status'] === 'published' && !$berita->published_at && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $oldValues = $berita->getOriginal();
        $berita->update($validated);

        $this->logAudit('update', $berita, $oldValues);

        return redirect()->route('kecamatan.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Soft delete berita.
     */
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        $this->authorize('delete', $berita);
        $berita->delete();

        $this->logAudit('delete', $berita);

        return redirect()->route('kecamatan.berita.index')->with('success', 'Berita berhasil dihapus (Arsip).');
    }

    /**
     * Toggle status publish/draft.
     */
    public function toggleStatus($id)
    {
        $berita = Berita::findOrFail($id);
        $this->authorize('update', $berita);
        $newStatus = $berita->status === 'published' ? 'draft' : 'published';

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'published' && !$berita->published_at) {
            $updateData['published_at'] = now();
        }

        $oldValues = $berita->getOriginal();
        $berita->update($updateData);

        $this->logAudit('toggle_status', $berita, $oldValues);

        return back()->with('success', "Status berita berhasil diubah menjadi $newStatus.");
    }

    /**
     * Helper untuk pencatatan Audit Log.
     */
    private function logAudit($action, $model, $oldValues = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'details' => "Aksi $action pada modul Berita: " . $model->judul,
            'old_values' => $oldValues,
            'new_values' => $model->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'domain' => 'kecamatan'
        ]);
    }
}
