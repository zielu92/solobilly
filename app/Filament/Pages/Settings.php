<?php

namespace App\Filament\Pages;

use App\Models\Currency;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
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
                                ->options(Currency::all()->pluck('code', 'id')),
                            Select::make('general.default_currency')
                                ->label(__('settings.default_currency'))
                                ->options(function (Get $get) {
                                    return Currency::whereIn('id', $get('general.currencies'))->get()->pluck('code', 'id');
                                }),
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
                            TextInput::make('seller_.hone')
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
                                    '23' => '23%',
                                    '22' => '22%',
                                    '8' => '8%',
                                    '5' => '5%',
                                    '0' => '0%',
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
