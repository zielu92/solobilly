<?php

namespace App\Filament\Pages;

use App\Models\Currency;
use App\Models\Tax;
use Chiiya\FilamentAccessControl\Traits\AuthorizesPageAccess;
use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    use AuthorizesPageAccess;
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = 'Settings';
    public static function getNavigationLabel(): string
    {
        return __('settings.settings');
    }

    public function getFormActions() : array
    {
        return [
            Action::make('save')
                ->label(__('settings.save_changes'))
                ->submit('data')
                ->keyBindings(['mod+s'])
        ];
    }

    public static string $permission = 'settings.view';

    public static function canAccess(): bool
    {
        return Filament::auth()->user()->can(static::$permission);
    }

    public function mount(): void
    {
        if (!request()->header('X-Livewire')) {
            static::authorizePageAccess();
        }
        parent::mount();
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make('General')
                ->label(__('settings.general'))
                ->schema([
                    Tabs\Tab::make('General')
                        ->label(__('settings.general'))
                        ->columns(4)
                        ->schema([
                            Select::make('general.currencies')
                                ->label(__('settings.basic_currencies'))
                                ->live()
                                ->multiple()
                                ->options(Currency::all()->pluck('code', 'id'))
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    // Get current default currency
                                    $currentDefault = $get('general.default_currency');

                                    // If current default is not in the new selection, clear it
                                    if ($currentDefault && !in_array($currentDefault, $state ?? [])) {
                                        $set('general.default_currency', null);
                                    }
                                }),
                            Select::make('general.default_currency')
                                ->label(__('settings.default_currency'))
                                ->live()

                                ->options(function (Get $get) {
                                    $currencyIds = $get('general.currencies') ?? [];

                                    if (empty($currencyIds)) {
                                        return [];
                                    }

                                    return Currency::whereIn('id', $currencyIds)->get()->pluck('code', 'id');
                                })
                        ]),
                    Tabs\Tab::make('Seller')
                        ->label(__('settings.seller'))
                        ->columns(4)
                        ->schema([
                            TextInput::make('seller.company_name')
                                ->label(__('settings.company_name'))
                                ->required(),
                            TextInput::make('seller.address')
                                ->label(__('settings.company_address_street'))
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.city')
                                ->label(__('settings.company_address_city'))
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.postal_code')
                                ->label(__('settings.company_address_postal'))
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.country')
                                ->label(__('settings.company_address_country'))
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.nip')
                                ->label(__('settings.company_tax_number'))
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.email')
                                ->label(__('settings.seller_email'))
                                ->email()
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.phone')
                                ->label(__('settings.seller_phone'))
                                ->tel()
                                ->maxLength(255)
                                ->required(),
                        ]),
                    Tabs\Tab::make('Invoice')
                        ->label(__('settings.invoice'))
                        ->columns(4)
                        ->schema([
                            TextInput::make('invoice.default_issuer')
                                ->label(__('settings.default_invoice_issuer'))
                                ->maxLength(255)
                                ->columnSpan(2)
                                ->required(),
                            TextInput::make('invoice.default_place')
                                ->label(__('settings.default_invoice_issue_place'))
                                ->maxLength(255)
                                ->columnSpan(2)
                                ->required(),
                            Select::make('invoice.default_tax_rate')
                                ->label(__('settings.default_vat_rate'))
                                ->columnSpan(2)
                                ->options([
                                    ...Tax::all()->pluck('name', 'rate'),
                                    'zw' => __('settings.tax_rates.zw'),
                                    'np' => __('settings.tax_rates.np'),
                                ]),
                            TextInput::make('invoice.default_pattern')
                                ->label(__('settings.default_invoice_number_generator_pattern'))
                                ->maxLength(255)
                                ->columnSpan(2)
                                ->placeholder('{nm}/{m}/{y}')
                                ->default('{nm}/{m}/{y}')
                                ->helperText("
                                {nm} - ".__('settings.previous_invoice_number_for_this_month').";
                                {ny} - ".__('settings.previous_invoice_number_for_this_year').";
                                {m} - ".__('settings.current_month_number').";
                                {y} - ".__('settings.current_month_number').";
                                {random} - ".__('settings.random_number').";
                            ")
                        ]),
                ]),
        ];
    }
}
