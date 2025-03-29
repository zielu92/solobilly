<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Backups;
use App\Filament\Resources\BuyerResource;
use App\Filament\Resources\CostCategoryResource;
use App\Filament\Resources\CostResource;
use App\Filament\Resources\InvoiceResource;
use App\Models\CostCategory;
use Coolsam\Modules\ModulesPlugin;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Settings;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->globalSearch()
            ->brandName('Solo Billy')
            ->id('app')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        NavigationItem::make('Dashboard')
                            ->label(__('nav.dashboard'))
                            ->icon('heroicon-o-home')
                            ->url(fn(): string => Dashboard::getUrl()),
                    ])->groups([
                        NavigationGroup::make('Invoices')->icon('heroicon-o-document-currency-dollar')
                            ->label(__('nav.invoices'))
                            ->items([
                                ...InvoiceResource::getNavigationItems(),
                                ...BuyerResource::getNavigationItems()
                            ]),
                        NavigationGroup::make('Costs')->icon('heroicon-o-fire')
                            ->label(__('nav.costs'))
                            ->items([
                                ...CostResource::getNavigationItems(),
                                ...CostCategoryResource::getNavigationItems()
                            ]),
                        NavigationGroup::make('Settings')->icon('heroicon-o-cog')
                            ->label(__('nav.settings'))
                            ->collapsed()
                            ->items([
                                ...Settings::getNavigationItems(),
                                ...Backups::getNavigationItems(),
                                ...PaymentMethodResource::getNavigationItems(),
                                ...ExchangeRateResource::getNavigationItems(),
                            ]),
                    ]);
            })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([

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
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentSpatieLaravelBackupPlugin::make(),
                ModulesPlugin::make(),
                FilamentSettingsPlugin::make()
                    ->pages([
                        Settings::class,
                    ])
            ]);
    }
}
