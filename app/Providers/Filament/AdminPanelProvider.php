<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\Team;
use Illuminate\View\View;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Widgets\SiteStatsWidget;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Pages\Tenancy\RegisterTeam;
use Filament\Http\Middleware\IdentifyTenant;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Althinect\FilamentSpatieRolesPermissions\Middleware\SyncSpatiePermissionsWithFilamentTenants;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->default()
            ->path('')
            ->tenant(Team::class, 'id', 'team')
            ->login()
            ->registration()
            ->emailVerification()
            ->passwordReset()
            ->tenantRegistration(RegisterTeam::class)
            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Zinc,
            ])
            ->viteTheme(['resources/css/filament/admin/theme.css', 'resources/js/filament/admin/scroll-fix.js'])
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
                // \Awcodes\Overlook\Widgets\OverlookWidget::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
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
                IdentifyTenant::class,
            ])
            ->authMiddleware([
                'auth',
                Authenticate::class,
            ])->tenantMiddleware([
                SyncSpatiePermissionsWithFilamentTenants::class,
            ], isPersistent: true)
            ->plugins([
                \Awcodes\LightSwitch\LightSwitchPlugin::make(),
                \Awcodes\FilamentQuickCreate\QuickCreatePlugin::make(),
                \Awcodes\FilamentVersions\VersionsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                \Awcodes\Curator\CuratorPlugin::make()
                    ->label('Media')
                    ->pluralLabel('Media')
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationSort(3)
                    ->navigationCountBadge(),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile()
                    ->enableTwoFactorAuthentication(),
                // \Awcodes\Overlook\OverlookPlugin::make()
                //     ->sort(2)
                //     ->columns([
                //         'default' => 1,
                //         'sm' => 2,
                //         'md' => 3,
                //         'lg' => 4,
                //         'xl' => 5,
                //         '2xl' => null,
                //     ]),
                \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin::make(),
                \FilipFonal\FilamentLogManager\FilamentLogManager::make(),
            ])->maxContentWidth(MaxWidth::Full)
            ->renderHook('panels::topbar.start', fn (): View => view('filament.app.hooks.welcome_user'))
            ->sidebarCollapsibleOnDesktop()->spa();
    }
}
