<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $appProfile = appProfile();
        $brandName = $appProfile->region_level . ' ' . $appProfile->region_name;
        $logoUrl = $appProfile->logo_path ? asset('storage/' . $appProfile->logo_path) : null;

        return $panel
            ->id('admin')
            ->path('kecamatan/manajemen')
            ->login()
            ->profile()
            ->brandName($brandName)
            ->brandLogo($logoUrl)
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Slate,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Teal,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
            ])
            ->font('Outfit')
            ->sidebarCollapsibleOnDesktop()
            ->navigationItems([
                NavigationItem::make('Beranda Dashboard')
                    ->url(fn(): string => route('kecamatan.dashboard'))
                    ->icon('heroicon-o-home')
                    ->group('DASHBOARD UTAMA')
                    ->sort(-1),
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->assets([
                \Filament\Support\Assets\Css::make('custom-stylesheet', asset('css/filament-custom.css')),
            ]);
    }
}
