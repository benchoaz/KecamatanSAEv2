<?php

namespace App\Observers;

use App\Models\Aspek;
use Illuminate\Support\Facades\Cache;

class AspekObserver
{
    public $afterCommit = true;

    public function saved(Aspek $aspek): void
    {
        $this->clearCache($aspek);
    }

    public function deleted(Aspek $aspek): void
    {
        $this->clearCache($aspek);
    }

    protected function clearCache(Aspek $aspek): void
    {
        Cache::forget("master.aspek.{$aspek->menu_id}");
        // Also clear menus cache if aspect counts/relations impact it? 
        // For now sticking to explicit dependencies. If Menu has 'with aspect', we might need to clear menu cache?
        // User pattern suggests explicit targeting.
    }
}
