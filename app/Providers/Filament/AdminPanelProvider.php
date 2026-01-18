<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsIconAlias;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        $navigationGroups = [
            NavigationGroup::make()
                ->label(__('menu.groups.administration')),
            NavigationGroup::make()
                ->label(__('menu.groups.authentication')),
            NavigationGroup::make()
                ->label(__('menu.groups.cms')),
            NavigationGroup::make()
                ->label(__('menu.groups.settings')),
        ];

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->homeUrl(fn (): string => OrderResource::getUrl('index'))
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->icons([
                PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON => 'heroicon-o-bars-3',
                PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL => 'heroicon-o-bars-3',
                PanelsIconAlias::SIDEBAR_EXPAND_BUTTON => 'heroicon-o-bars-3',
                PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL => 'heroicon-o-bars-3',
            ])
            ->colors([
                'primary' => Color::hex('#d61c03'),
            ])
            ->brandLogo(asset('images/ketchup.jpg'))
            ->brandLogoHeight('3rem')
            ->renderHook(
                'panels::body.start',
                fn () => view('filament.hooks.login-styles')
            )
            ->renderHook(
                'panels::resource.pages.list-records.table.before',
                fn () => view('filament.hooks.orders-realtime')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups($navigationGroups);
    }
}
