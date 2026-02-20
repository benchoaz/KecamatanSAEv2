<?php

namespace App\Observers;

use App\Models\Indikator;
use Illuminate\Support\Facades\Cache;

class IndikatorObserver
{
    public $afterCommit = true;

    public function saved(Indikator $indikator): void
    {
        $this->clearCache($indikator);
    }

    public function deleted(Indikator $indikator): void
    {
        $this->clearCache($indikator);
    }

    protected function clearCache(Indikator $indikator): void
    {
        Cache::forget("master.indikator.{$indikator->aspek_id}");
    }
}
