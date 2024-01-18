<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Widgets\SiteStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->viteTheme(['resources/css/filament/admin/theme.css', 'resources/js/filament/admin/scroll-fix.js'])
            ->id('admin')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                'Site Vigilance',
                'User Management',
                'System'
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                SiteStatsWidget::class,
            ])
            ->databaseNotifications()
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
                'auth',
                Authenticate::class,
            ])->plugins([
                \Awcodes\LightSwitch\LightSwitchPlugin::make(),
                \Awcodes\FilamentQuickCreate\QuickCreatePlugin::make(),
                \Awcodes\FilamentVersions\VersionsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \Awcodes\Curator\CuratorPlugin::make()
                    ->label('Media')
                    ->pluralLabel('Media')
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationSort(3)
                    ->navigationCountBadge(),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile()
                    ->enableTwoFactorAuthentication(),
                \Awcodes\Overlook\OverlookPlugin::make()
                    ->sort(2)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 5,
                        '2xl' => null,
                    ]),
                \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin::make(),
                \FilipFonal\FilamentLogManager\FilamentLogManager::make(),
            ])->maxContentWidth(MaxWidth::Full)
            ->spa()
            ->widgets([
                \Awcodes\Overlook\Widgets\OverlookWidget::class,
            ])->sidebarCollapsibleOnDesktop();
    }
}
