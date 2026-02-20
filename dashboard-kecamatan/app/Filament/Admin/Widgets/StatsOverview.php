<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', \App\Models\User::count())
                ->description('Pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Desa', \App\Models\Desa::count())
                ->description('Desa di Kecamatan')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('info'),
            Stat::make('Berita Published', \App\Models\Berita::where('status', 'published')->count())
                ->description('Berita aktif')
                ->descriptionIcon('heroicon-m-news-papers')
                ->color('warning'),
            Stat::make('Pengumuman Aktif', \App\Models\Announcement::where('is_active', true)->count())
                ->description('Pengumuman saat ini')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('danger'),
        ];
    }
}
