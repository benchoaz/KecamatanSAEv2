<?php

namespace App\Services;

use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Cache;

class ModuleSettingsService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected int $cacheDuration = 3600;

    /**
     * Get a setting value by module and key
     *
     * @param string $module Module name (trantibum, umkm, loker, pelayanan)
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public function get(string $module, string $key, $default = null)
    {
        $cacheKey = "module_settings.{$module}.{$key}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($module, $key, $default) {
            return ModuleSetting::getValue($module, $key, $default);
        });
    }

    /**
     * Set a setting value
     *
     * @param string $module Module name
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string $type Value type (string, boolean, json, integer)
     * @param string|null $description Optional description
     * @return ModuleSetting
     */
    public function set(string $module, string $key, $value, string $type = 'string', ?string $description = null): ModuleSetting
    {
        $setting = ModuleSetting::setValue($module, $key, $value, $type, $description);

        // Clear cache for this setting
        $this->clearCache($module, $key);

        return $setting;
    }

    /**
     * Get all settings for a module
     *
     * @param string $module Module name
     * @return array
     */
    public function all(string $module): array
    {
        $cacheKey = "module_settings.{$module}.all";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($module) {
            return ModuleSetting::where('module', $module)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting->typed_value];
                })
                ->toArray();
        });
    }

    /**
     * Check if a setting exists
     *
     * @param string $module Module name
     * @param string $key Setting key
     * @return bool
     */
    public function has(string $module, string $key): bool
    {
        return ModuleSetting::where('module', $module)
            ->where('key', $key)
            ->exists();
    }

    /**
     * Delete a setting
     *
     * @param string $module Module name
     * @param string $key Setting key
     * @return bool
     */
    public function delete(string $module, string $key): bool
    {
        $deleted = ModuleSetting::where('module', $module)
            ->where('key', $key)
            ->delete();

        $this->clearCache($module, $key);

        return $deleted > 0;
    }

    /**
     * Clear cache for a specific setting
     *
     * @param string $module Module name
     * @param string $key Setting key
     */
    public function clearCache(string $module, string $key): void
    {
        Cache::forget("module_settings.{$module}.{$key}");
        Cache::forget("module_settings.{$module}.all");
    }

    /**
     * Clear all cache for a module
     *
     * @param string $module Module name
     */
    public function clearModuleCache(string $module): void
    {
        // Get all keys for this module
        $keys = ModuleSetting::where('module', $module)->pluck('key');

        foreach ($keys as $key) {
            Cache::forget("module_settings.{$module}.{$key}");
        }

        Cache::forget("module_settings.{$module}.all");
    }

    /**
     * Get module announcements (helper for view composers)
     * Uses target_type field to filter announcements for specific modules
     *
     * @param string $module Module name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModuleAnnouncements(string $module)
    {
        // Map module names to target_type values
        $targetTypeMap = [
            'trantibum' => 'trantibum',
            'umkm' => 'umkm',
            'loker' => 'loker',
            'pelayanan' => 'pelayanan',
        ];

        $targetType = $targetTypeMap[$module] ?? $module;

        return \App\Models\Announcement::where('target_type', $targetType)
            ->where('is_active', true)
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get Trantibum statistics for dashboard
     *
     * @return array
     */
    public function getTrantibumStats(): array
    {
        return [
            'total_kejadian' => \App\Models\TrantibumKejadian::count(),
            'kejadian_bulan_ini' => \App\Models\TrantibumKejadian::whereMonth('waktu_kejadian', now()->month)
                ->whereYear('waktu_kejadian', now()->year)
                ->count(),
            'total_relawan' => \App\Models\TrantibumRelawan::count(),
            'kejadian_belum_ditangani' => \App\Models\TrantibumKejadian::where('status', '!=', 'Selesai')->count(),
        ];
    }

    /**
     * Get UMKM statistics for dashboard
     *
     * @return array
     */
    public function getUmkmStats(): array
    {
        return [
            'total_umkm' => \App\Models\UmkmLocal::count(),
            'umkm_aktif' => \App\Models\UmkmLocal::where('is_active', true)
                ->where('is_verified', true)
                ->where('is_flagged', false)
                ->count(),
            'umkm_pending' => \App\Models\UmkmLocal::where(function ($query) {
                $query->where('is_active', false)
                    ->orWhere('is_verified', false)
                    ->orWhere('is_flagged', true);
            })->count(),
            'total_produk' => \App\Models\UmkmProduct::count(),
        ];
    }

    /**
     * Get Loker statistics for dashboard
     *
     * @return array
     */
    public function getLokerStats(): array
    {
        return [
            'total_loker' => \App\Models\Loker::count(),
            'loker_aktif' => \App\Models\Loker::where('status', 'aktif')->count(),
            'loker_pending' => \App\Models\Loker::where('status', 'pending')->count(),
            'loker_ditutup' => \App\Models\Loker::where('status', 'ditutup')->count(),
        ];
    }
}
