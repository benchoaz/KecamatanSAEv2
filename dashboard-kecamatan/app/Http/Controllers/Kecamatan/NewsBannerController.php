<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\NewsBanner;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsBannerController extends Controller
{
    public function index()
    {
        // This will be handled by BeritaController@index as part of the tabs
        return redirect()->route('kecamatan.berita.index', ['tab' => 'banners']);
    }

    public function create()
    {
        return view('kecamatan.berita.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('berita/banners', 'public');
            $validated['image_path'] = $path;
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $banner = NewsBanner::create($validated);

        $this->logAudit('create', $banner);

        return redirect()->route('kecamatan.berita.index', ['tab' => 'banners'])
            ->with('success', 'Banner iklan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $banner = NewsBanner::findOrFail($id);
        return view('kecamatan.berita.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = NewsBanner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'priority' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $path = $request->file('image')->store('berita/banners', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');

        $oldValues = $banner->getOriginal();
        $banner->update($validated);

        $this->logAudit('update', $banner, $oldValues);

        return redirect()->route('kecamatan.berita.index', ['tab' => 'banners'])
            ->with('success', 'Banner iklan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $banner = NewsBanner::findOrFail($id);
        
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        
        $banner->delete();

        $this->logAudit('delete', $banner);

        return redirect()->route('kecamatan.berita.index', ['tab' => 'banners'])
            ->with('success', 'Banner iklan berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $banner = NewsBanner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);

        $this->logAudit('toggle_status', $banner);

        return back()->with('success', 'Status banner berhasil diubah.');
    }

    private function logAudit($action, $model, $oldValues = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'details' => "Aksi $action pada modul Banner Berita: " . $model->title,
            'old_values' => $oldValues,
            'new_values' => $model->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'domain' => 'kecamatan'
        ]);
    }
}
