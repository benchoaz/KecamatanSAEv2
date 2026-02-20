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
        $publicAnnouncements = Announcement::where('target_type', 'public')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $latestBerita = Berita::published()
            ->with('author:id,nama_lengkap')
            ->latest('published_at')
            ->take(4)
            ->get();

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

        // New data for overhauled landing page
        $featuredLayanan = PelayananFaq::where('is_active', true)
            ->where('category', '!=', 'Darurat')
            ->take(5)
            ->get();

        $masterLayanan = MasterLayanan::where('is_active', true)->orderBy('urutan')->get();

        $resolvedComplaints = PublicService::where('status', 'selesai')
            ->latest()
            ->take(3)
            ->get();

        $umkms = Umkm::where('status', 'aktif')->latest()->take(6)->get();
        $jobs = JobVacancy::where('is_active', true)->latest()->take(4)->get();
        $desas = Desa::orderBy('nama_desa')->get();

        // Work Directory - Latest jobs and services
        $workItems = WorkDirectory::public()->latest()->take(6)->get();

        // Hero Section Settings
        $profileService = app(ApplicationProfileService::class);
        $heroBg = $profileService->getHeroBg();
        $bgOpacity = $profileService->getHeroBgOpacity();
        $bgBlur = $profileService->getHeroBgBlur();
        $isHeroActive = $profileService->isHeroImageActive();
        $heroImage = $profileService->getHeroImage();
        $heroImageAlt = $profileService->getHeroImageAlt();

        return view('landing', compact(
            'publicAnnouncements',
            'latestBerita',
            'faqKeywords',
            'featuredLayanan',
            'masterLayanan',
            'resolvedComplaints',
            'umkms',
            'jobs',
            'desas',
            'workItems',
            'heroBg',
            'bgOpacity',
            'bgBlur',
            'isHeroActive',
            'heroImage',
            'heroImageAlt'
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
}
