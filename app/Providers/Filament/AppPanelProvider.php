<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Backups;
use App\Filament\Pages\Settings;
use App\Filament\Resources\BuyerResource;
use App\Filament\Resources\CostCategoryResource;
use App\Filament\Resources\CostResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\TaxResource;
use App\Filament\Resources\WorkLogResource;
use App\Filament\Widgets\InvoiceCostsStatWidget;
use Chiiya\FilamentAccessControl\FilamentAccessControlPlugin;
use Chiiya\FilamentAccessControl\Resources\FilamentUserResource;
use Chiiya\FilamentAccessControl\Resources\PermissionResource;
use Chiiya\FilamentAccessControl\Resources\RoleResource;
use Coolsam\Modules\ModulesPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Modules\ExchangeRates\Filament\Widgets\ExchangeRatesWidget;
use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AppPanelProvider extends PanelProvider
{
    /**
     * Configures and returns the application's Filament panel with custom branding, navigation, resources, middleware, and plugins.
     *
     * Sets up the panel with global search, a custom brand name, primary color, authentication, and resource discovery. Defines a structured navigation menu with dashboard, invoices, costs, and settings groups. Registers middleware for session, authentication, and CSRF protection, enables a collapsible sidebar, and adds plugins for backups, modules, and settings management.
     *
     * @param Panel $panel The Filament panel instance to configure.
     * @return Panel The configured Filament panel instance.
     * @throws \Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->globalSearch()
            ->brandName('Solo Billy')
            ->id('admin')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class
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
                                ...BuyerResource::getNavigationItems(),
                                ...WorkLogResource::getNavigationItems(),
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
                                ...TaxResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Users')->icon('heroicon-o-users')
                            ->label(__('nav.users'))
                            ->collapsed()
                            ->items([
                                ...FilamentUserResource::getNavigationItems(),
                                ...RoleResource::getNavigationItems(),
                            ])
                    ]);
            })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                InvoiceCostsStatWidget::class,
                ExchangeRatesWidget::class,
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
                FilamentAccessControlPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make(),
                ModulesPlugin::make(),
                FilamentSettingsPlugin::make()
                    ->pages([
                        Settings::class,
                    ])
            ]);
    }
}
