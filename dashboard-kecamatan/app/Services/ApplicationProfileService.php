<?php

namespace App\Services;

use App\Models\AppProfile;
use Illuminate\Support\Facades\Cache;

class ApplicationProfileService
{
    protected $cacheKey = 'app_profile_global';

    public function getProfile()
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return AppProfile::first() ?? new AppProfile([
                'app_name' => 'Kecamatan SAE',
                'region_name' => 'Kecamatan Besuk',
                'region_level' => 'kecamatan',
                'tagline' => 'Solusi Administrasi Terpadu',
            ]);
        });
    }

    public function getAppName()
    {
        return $this->getProfile()->app_name;
    }

    public function getRegionName()
    {
        return $this->getProfile()->region_name;
    }

    public function getRegionLevel()
    {
        return $this->getProfile()->region_level;
    }

    public function getTagline()
    {
        return $this->getProfile()->tagline;
    }

    public function getLogo()
    {
        $path = $this->getProfile()->logo_path;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getUmkmImage()
    {
        $path = $this->getProfile()->image_umkm;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getPariwisataImage()
    {
        $path = $this->getProfile()->image_pariwisata;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getFestivalImage()
    {
        $path = $this->getProfile()->image_festival;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getHeroImage()
    {
        $path = $this->getProfile()->hero_image_path;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getHeroImageAlt()
    {
        return $this->getProfile()->hero_image_alt ?? 'Pimpinan Daerah';
    }

    public function isHeroImageActive()
    {
        return (bool) ($this->getProfile()->hero_image_active ?? false);
    }

    public function getHeroBg()
    {
        $path = $this->getProfile()->hero_bg_path;
        return $path ? asset('storage/' . $path) : null;
    }

    public function getHeroBgOpacity()
    {
        return $this->getProfile()->hero_bg_opacity ?? 10;
    }

    public function getHeroBgBlur()
    {
        return $this->getProfile()->hero_bg_blur ?? 6;
    }

    public function getWhatsappBotNumber()
    {
        return $this->getProfile()->whatsapp_bot_number;
    }

    public function getWhatsappBotUrl($text = "Halo, saya butuh informasi.")
    {
        $number = $this->getWhatsappBotNumber();
        if (!$number) {
            return '#';
        }

        // Clean number (remove +, spaces, dashes)
        $cleanNumber = preg_replace('/[^0-9]/', '', $number);

        return "https://wa.me/{$cleanNumber}?text=" . urlencode($text);
    }

    public function clearCache()
    {
        Cache::forget($this->cacheKey);
    }
}
