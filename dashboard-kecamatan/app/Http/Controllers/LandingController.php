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
    
    public function statistik()
    {
        $common = $this->prepareStatistikData();
        $desas = $common['desas'];

        // Calculate Overview Summary Safely
        $summary = [
            'pendidikan_tinggi' => $desas->sum(function($d) {
                $stat = $d->stat_pendidikan;
                if (is_string($stat)) $stat = json_decode($stat, true);
                if (!is_array($stat)) return 0;
                $item = collect($stat)->firstWhere('nama', 'Sarjana') ?? collect($stat)->firstWhere('nama', 'S1');
                return $item['jumlah'] ?? 0;
            }),
            'stunting_cases' => $desas->sum(function($d) {
                $stat = $d->stat_kesehatan;
                if (is_string($stat)) $stat = json_decode($stat, true);
                if (!is_array($stat)) return 0;
                return $stat['totalStunting'] ?? 0;
            }),
            'kk_total' => $desas->sum('jumlah_kk'),
            'poverty_avg' => $desas->avg(function($d) {
                $stat = $d->stat_desil;
                if (is_string($stat)) $stat = json_decode($stat, true);
                if (!is_array($stat)) return 0;
                return $stat['totalDesil1'] ?? 0;
            }),
        ];

        return view('landing.statistik.index', array_merge($common, ['summary' => $summary]));
    }

    public function statPendidikan()
    {
        $common = $this->prepareStatistikData();
        $statPendidikan = [];
        foreach ($common['desas'] as $desa) {
            $stats = $desa->stat_pendidikan ?? [];
            foreach ($stats as $item) {
                $nama = $item['nama'] ?? '';
                if ($nama) $statPendidikan[$nama] = ($statPendidikan[$nama] ?? 0) + ($item['jumlah'] ?? 0);
            }
        }
        arsort($statPendidikan);
        $topPendidikan = array_keys(array_slice($statPendidikan, 0, 8));
        
        return view('landing.statistik.pendidikan', array_merge($common, compact('statPendidikan', 'topPendidikan')));
    }

    public function statPekerjaan()
    {
        $common = $this->prepareStatistikData();
        $statPekerjaan = [];
        foreach ($common['desas'] as $desa) {
            $stats = $desa->stat_pekerjaan ?? [];
            foreach ($stats as $item) {
                $nama = $item['nama'] ?? '';
                if ($nama) $statPekerjaan[$nama] = ($statPekerjaan[$nama] ?? 0) + ($item['jumlah'] ?? 0);
            }
        }
        arsort($statPekerjaan);
        $topPekerjaan = array_keys(array_slice($statPekerjaan, 0, 8));

        return view('landing.statistik.pekerjaan', array_merge($common, compact('statPekerjaan', 'topPekerjaan')));
    }

    public function statAgama()
    {
        $common = $this->prepareStatistikData();
        $statAgama = [];
        foreach ($common['desas'] as $desa) {
            $stats = $desa->stat_agama ?? [];
            foreach ($stats as $item) {
                $nama = $item['nama'] ?? '';
                if ($nama) $statAgama[$nama] = ($statAgama[$nama] ?? 0) + ($item['jumlah'] ?? 0);
            }
        }
        arsort($statAgama);
        $topAgama = array_keys(array_slice($statAgama, 0, 5));

        return view('landing.statistik.agama', array_merge($common, compact('statAgama', 'topAgama')));
    }

    public function statKesehatan()
    {
        $common = $this->prepareStatistikData();
        return view('landing.statistik.kesehatan', $common);
    }

    public function statKesejahteraan()
    {
        $common = $this->prepareStatistikData();
        return view('landing.statistik.kesejahteraan', $common);
    }

    /**
     * Helper to prepare common data for all statistik pages
     */
    private function prepareStatistikData()
    {
        $desas = Desa::orderBy('nama_desa', 'asc')->get();
        
        $totalPenduduk = $desas->sum('jumlah_penduduk');
        $totalLaki = $desas->sum('jumlah_laki_laki');
        $totalPerempuan = $desas->sum('jumlah_perempuan');
        $totalKk = $desas->sum('jumlah_kk');
        $totalLuas = $desas->sum('luas_wilayah');
        
        $demografiStats = [
            'total_penduduk' => $totalPenduduk,
            'total_laki' => $totalLaki,
            'total_perempuan' => $totalPerempuan,
            'total_kk' => $totalKk,
            'total_luas' => $totalLuas,
        ];

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

        $publicAnnouncements = Announcement::where('target_type', 'public')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'desas' => $desas,
            'demografiStats' => $demografiStats,
            'publicAnnouncements' => $publicAnnouncements,
            'heroBg' => $heroBg,
            'bgOpacity' => $bgOpacity,
            'bgBlur' => $bgBlur,
            'isHeroActive' => $isHeroActive,
            'heroImage' => $heroImage,
            'heroImageAlt' => $heroImageAlt,
            'faqKeywords' => $faqKeywords,
        ];
    }

    public function berita()
    {
        $profileService = app(ApplicationProfileService::class);
        $appProfile = $profileService->getProfile();
        $berita = Berita::published()->latest()->paginate(9);
        return view('public.berita.index', compact('berita', 'appProfile'));
    }
}
