<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Berita;
use App\Models\PelayananFaq;
use App\Models\PublicService;
use App\Models\UmkmLocal;
use App\Models\JobVacancy;
use App\Models\WorkDirectory;
use App\Models\MasterLayanan;
use App\Models\Umkm;
use App\Models\Desa;
use App\Services\ApplicationProfileService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $profileService = app(\App\Services\ApplicationProfileService::class);
        $appProfile = $profileService->getProfile();

        $publicAnnouncements = \App\Models\Announcement::where('target_type', 'public')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $heroBg = $profileService->getHeroBg();
        $bgOpacity = $profileService->getHeroBgOpacity();
        $bgBlur = $profileService->getHeroBgBlur();
        $isHeroActive = $profileService->isHeroImageActive();
        $heroImage = $profileService->getHeroImage();
        $heroImageAlt = $profileService->getHeroImageAlt();
        $whatsappUrl = $profileService->getWhatsappBotUrl('MENU');

        // Other required vars for the view
        $latestBerita = \App\Models\Berita::published()->latest()->take(3)->get();
        $faqKeywords = [];
        $featuredLayanan = \App\Models\MasterLayanan::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('urutan')
            ->get();
        $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
        $resolvedComplaints = \App\Models\PublicService::where('status', 'Selesai')->take(5)->get();
        $desas = \App\Models\Desa::all();

        // Data UMKM & Produk untuk Etalase Landing Page
        $officialUmkms = \App\Models\Umkm::where('status', 'aktif')->latest()->take(3)->get();
        $featuredProducts = \App\Models\UmkmLocal::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();
            
        // Jika tidak ada featured_product, ambil yang terbaru saja
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = \App\Models\UmkmLocal::where('is_active', true)->latest()->take(4)->get();
        }

        return view('landing', compact(
            'publicAnnouncements',
            'latestBerita',
            'faqKeywords',
            'featuredLayanan',
            'masterLayanan',
            'resolvedComplaints',
            'desas',
            'officialUmkms',
            'featuredProducts',
            'heroBg',
            'bgOpacity',
            'bgBlur',
            'isHeroActive',
            'heroImage',
            'heroImageAlt',
            'whatsappUrl',
            'appProfile'
        ));
    }
    
    public function wilayah()
    {
        $desas = Desa::orderBy('nama_desa')->get();

        // Settings for Header/Footer consistency
        $profileService = app(ApplicationProfileService::class);
        $heroBg = $profileService->getHeroBg();
        $bgOpacity = $profileService->getHeroBgOpacity();
        $bgBlur = $profileService->getHeroBgBlur();
        $isHeroActive = $profileService->isHeroImageActive();
        $heroImage = $profileService->getHeroImage();
        $heroImageAlt = $profileService->getHeroImageAlt();

        // FAQ Keywords for Voice Guide
        $faqKeywords = PelayananFaq::where('is_active', true)
            ->pluck('keywords')
            ->filter()
            ->flatMap(function ($k) {
                return explode(',', $k);
            })
            ->map(function ($k) {
                return trim(strtolower($k));
            })
            ->unique()
            ->values()
            ->toArray();

        // Public Announcements for header (if needed)
        $publicAnnouncements = Announcement::where('target_type', 'public')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('landing.wilayah', compact(
            'desas',
            'publicAnnouncements',
            'heroBg',
            'bgOpacity',
            'bgBlur',
            'isHeroActive',
            'heroImage',
            'heroImageAlt',
            'faqKeywords'
        ));
    }

    public function berita()
    {
        $profileService = app(ApplicationProfileService::class);
        $appProfile = $profileService->getProfile();
        $berita = Berita::published()->latest()->paginate(9);
        return view('public.berita.index', compact('berita', 'appProfile'));
    }
}
