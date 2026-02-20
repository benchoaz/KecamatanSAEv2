<?php

namespace App\Observers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    /**
     * Handle events after all transactions are committed.
     */
    public $afterCommit = true;

    public function saved(Menu $menu): void
    {
        $this->clearCache($menu);
    }

    public function deleted(Menu $menu): void
    {
        $this->clearCache($menu);
    }

    protected function clearCache(Menu $menu): void
    {
        Cache::forget('master.menus');
        if ($menu->kode_menu) {
            Cache::forget("master.menu.kode.{$menu->kode_menu}");
        }
    }
}
