<?php

namespace App\Filament\Pages;

use App\Models\Currency;
use Closure;
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
        return 'General Settings';
    }
    public function schema(): array|Closure
    {
        return [
            Tabs::make('General')
                ->schema([
                    Tabs\Tab::make('General')
                        ->columns(4)
                        ->schema([
                            Select::make('general.currencies')
                                ->live()
                                ->label('Basic currencies')
                                ->multiple()
                                ->options(Currency::all()->pluck('code', 'id')),
                            Select::make('general.default_currency')
                                ->label('Default Currency')
                                ->options(function (Get $get) {
                                    return Currency::whereIn('id', $get('general.currencies'))->get()->pluck('code', 'id');
                                }),
                        ]),
                    Tabs\Tab::make('Seller')
                        ->columns(4)
                        ->schema([
                            TextInput::make('seller.company_name')
                                ->required(),
                            TextInput::make('seller.address')
                                ->label('Company Address (Street)')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.city')
                                ->label('Company Address (City)')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.postal_code')
                                ->label('Company Address (Postal Code)')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.country')
                                ->label('Company Address (Country)')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.nip')
                                ->label('Company NIP')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller.email')
                                ->label('Seller Email')
                                ->email()
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('seller_.hone')
                                ->label('Seller Phone')
                                ->tel()
                                ->maxLength(255)
                                ->required(),
                        ]),
                    Tabs\Tab::make('Invoice')
                        ->columns(4)
                        ->schema([
                            TextInput::make('invoice.default_issuer')
                                ->label('Default Invoice Issuer')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('invoice.default_place')
                                ->label('Default Invoice Issuance Place')
                                ->maxLength(255)
                                ->required(),
                            Select::make('invoice.default_tax_rate')
                                ->label('Default VAT rate')
                                ->options([
                                    '23' => '23%',
                                    '22' => '22%',
                                    '8' => '8%',
                                    '5' => '5%',
                                    '0' => '0%',
                                    'zw' => 'Exempt',
                                    'np' => 'Not Applicable',
                                ]),
                            TextInput::make('invoice.default_pattern')
                                ->label('Default Invoice Generation Pattern')
                                ->maxLength(255)
                                ->placeholder('{nm}/{m}/{y}')
                                ->default('{nm}/{m}/{y}')
                                ->helperText("
                                {nm} - Previous invoice number this month;
                                {ny} - Previous invoice number this year;
                                {m} - Current month number;
                                {y} - Current year number;
                                {random} - Random number;
                            ")
                        ]),
                ]),
        ];
    }
}
