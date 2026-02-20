<?php

namespace App\Services;

use App\Models\Announcement;
use Illuminate\Support\Facades\Cache;

class AnnouncementService
{
    public function getPublicAnnouncements()
    {
        return Cache::remember('active_announcements_public', 300, function () {
            return Announcement::active()
                ->where('target_type', 'public')
                ->where('display_mode', 'ticker')
                ->get();
        });
    }

    public function getDesaAnnouncements($desaId)
    {
        return Announcement::active()
            ->whereIn('target_type', ['all_desa', 'specific_desa'])
            ->get()
            ->filter(function ($announcement) use ($desaId) {
                if ($announcement->target_type == 'all_desa')
                    return true;
                if ($announcement->target_type == 'specific_desa' && is_array($announcement->target_desa_ids)) {
                    return in_array($desaId, $announcement->target_desa_ids);
                }
                return false;
            });
    }

    public function getInternalAnnouncements()
    {
        return Announcement::active()
            ->whereIn('target_type', ['internal', 'all_desa', 'specific_desa'])
            ->get();
    }
}
