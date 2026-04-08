<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Services\ModuleSettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\SubmissionRepositoryInterface::class,
            \App\Repositories\SubmissionRepository::class
        );

        // Register ModuleSettingsService as singleton
        $this->app->singleton(ModuleSettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \App\Models\Menu::observe(\App\Observers\MenuObserver::class);
        \App\Models\Aspek::observe(\App\Observers\AspekObserver::class);
        \App\Models\Indikator::observe(\App\Observers\IndikatorObserver::class);

        // Site Wide Announcements (Public) - With Caching
        view()->composer('landing', function ($view) {
            $announcements = Cache::remember('public_announcements', 1800, function () {
                return app(\App\Services\AnnouncementService::class)->getPublicAnnouncements();
            });
            $view->with('publicAnnouncements', $announcements);
        });

        // Desa Module Composer - With Caching
        view()->composer(['desa.*', 'layouts.desa'], function ($view) {
            if (auth()->check() && auth()->user()->desa_id) {
                $cacheKey = 'desa_announcements_' . auth()->user()->desa_id;
                $service = app(\App\Services\AnnouncementService::class);
                $view->with('internalAnnouncements', Cache::remember($cacheKey, 600, fn() => $service->getDesaAnnouncements(auth()->user()->desa_id)));
            }
        });

        // Trantibum Module Composer - Isolated
        view()->composer(['kecamatan.trantibum.*', 'layouts.trantibum'], function ($view) {
            if (auth()->check()) {
                $userRole = auth()->user()->role->nama_role ?? null;
                $allowedRoles = ['trantibum_admin', 'Super Admin', 'Operator Kecamatan'];

                if (in_array($userRole, $allowedRoles)) {
                    $moduleService = app(ModuleSettingsService::class);
                    $view->with('moduleAnnouncements', $moduleService->getModuleAnnouncements('trantibum'));
                    $view->with('trantibumStats', $moduleService->getTrantibumStats());
                }
            }
        });

        // UMKM Module Composer - Isolated
        view()->composer(['kecamatan.layanan.umkm.*', 'layouts.umkm-admin'], function ($view) {
            if (auth()->check()) {
                $userRole = auth()->user()->role->nama_role ?? null;
                $allowedRoles = ['umkm_admin', 'Super Admin', 'Operator Kecamatan'];

                if (in_array($userRole, $allowedRoles)) {
                    $moduleService = app(ModuleSettingsService::class);
                    $view->with('moduleAnnouncements', $moduleService->getModuleAnnouncements('umkm'));
                    $view->with('umkmStats', $moduleService->getUmkmStats());
                    $view->with('pendingUmkmCount', \App\Models\UmkmLocal::where('is_verified', false)->where('is_active', false)->count());
                }
            }
        });

        // Loker Module Composer - Isolated
        view()->composer(['kecamatan.layanan.loker.*', 'layouts.loker'], function ($view) {
            if (auth()->check()) {
                $userRole = auth()->user()->role->nama_role ?? null;
                $allowedRoles = ['loker_admin', 'Super Admin', 'Operator Kecamatan'];

                if (in_array($userRole, $allowedRoles)) {
                    $moduleService = app(ModuleSettingsService::class);
                    $view->with('moduleAnnouncements', $moduleService->getModuleAnnouncements('loker'));
                    $view->with('lokerStats', $moduleService->getLokerStats());
                    $view->with('pendingLokerCount', \App\Models\Loker::where('status', 'pending')->count());
                }
            }
        });

        // Kecamatan General Layout Composer (for shared components) - With Caching
        view()->composer(['layouts.kecamatan', 'kecamatan.dashboard.*'], function ($view) {
            if (auth()->check() && !auth()->user()->desa_id) {
                $service = app(\App\Services\AnnouncementService::class);
                $view->with('internalAnnouncements', Cache::remember('kecamatan_announcements', 600, fn() => $service->getInternalAnnouncements()));

                // Specific for Kecamatan: Service Submissions Notifications - Cached
                $view->with('unreadServiceCount', Cache::remember(
                    'unread_service_count',
                    300,
                    fn() =>
                    \App\Models\PublicService::whereIn('status', ['Menunggu', 'Menunggu Klarifikasi'])->count()
                ));
                $view->with('recentUnreadServices', \App\Models\PublicService::whereIn('status', ['Menunggu', 'Menunggu Klarifikasi'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get());
            }
        });
    }
}
