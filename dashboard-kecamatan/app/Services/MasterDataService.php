<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Aspek;
use App\Models\Indikator;
use Illuminate\Support\Facades\Cache;

class MasterDataService
{
    /**
     * Get all active menus with relationships loaded if possible (though for lists we might just need the menu).
     * Based on user request, maybe just the menus list for dropdowns.
     */
    public function getAllMenus()
    {
        return Cache::rememberForever('master.menus', function () {
            // Usually for dropdowns or lists we just need the menu itself
            return Menu::where('is_active', true)
                ->orderBy('urutan')
                ->get();
        });
    }

    public function getMenuByKode(string $kode)
    {
        // Cache individual menu lookup by kode
        return Cache::rememberForever("master.menu.kode.{$kode}", function () use ($kode) {
            return Menu::where('kode_menu', $kode)->firstOrFail();
        });
    }

    public function getAspekByMenu(int $menuId)
    {
        return Cache::rememberForever("master.aspek.{$menuId}", function () use ($menuId) {
            return Aspek::where('menu_id', $menuId)
                ->orderBy('urutan')
                ->get(); // Could select specific columns if optimization needed
        });
    }

    public function getIndikatorByAspek(int $aspekId)
    {
        return Cache::rememberForever("master.indikator.{$aspekId}", function () use ($aspekId) {
            return Indikator::where('aspek_id', $aspekId)
                ->orderBy('urutan')
                ->get();
        });
    }
}
