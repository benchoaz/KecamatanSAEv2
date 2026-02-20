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
    public function index()
    {
        $berita = Berita::published()
            ->with('author:id,nama_lengkap')
            ->latest('published_at')
            ->paginate(9);

        return view('public.berita.index', compact('berita'));
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

        // Audit view count (opsional, bisa dipindah ke service)
        $berita->increment('view_count');

        return view('public.berita.show', compact('berita'));
    }
}
