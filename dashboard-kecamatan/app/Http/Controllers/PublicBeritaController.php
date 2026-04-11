<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;

class PublicBeritaController extends Controller
{
    /**
     * Menampilkan daftar berita yang sudah dipublikasikan.
     * Tidak memerlukan login.
     */
    public function index(Request $request)
    {
        $query = Berita::published()
            ->with(['author:id,nama_lengkap', 'desa:id,nama_desa']);

        // Filter by Desa if requested
        if ($request->filled('desa_id')) {
            $query->where('desa_id', $request->desa_id);
        }

        $berita = $query->latest('published_at')->paginate(12);

        // Fetch popular news for sidebar
        $popularBerita = Berita::published()
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        // Fetch banners from specific news banners table
        $banners = \App\Models\NewsBanner::active()->get();

        // Fetch villages for the switcher
        $desas = \App\Models\Desa::where('status', 'aktif')
            ->orderBy('nama_desa', 'asc')
            ->get();

        // Optional: Count new news in last 24h per village
        $counts = Berita::where('published_at', '>=', now()->subDay())
            ->whereNotNull('desa_id')
            ->groupBy('desa_id')
            ->selectRaw('desa_id, count(*) as total')
            ->pluck('total', 'desa_id');

        return view('public.berita.index', compact('berita', 'desas', 'counts', 'popularBerita', 'banners'));
    }

    /**
     * Menampilkan detail berita berdasarkan slug.
     */
    public function show($slug)
    {
        $berita = Berita::published()
            ->with('author:id,nama_lengkap')
            ->where('slug', $slug)
            ->firstOrFail();

        $berita->increment('view_count');

        // Fetch popular news and banners for sidebar consistency
        $popularBerita = Berita::published()->orderBy('view_count', 'desc')->take(5)->get();
        $banners = \App\Models\NewsBanner::active()->get();

        return view('public.berita.show', compact('berita', 'popularBerita', 'banners'));
    }
}
