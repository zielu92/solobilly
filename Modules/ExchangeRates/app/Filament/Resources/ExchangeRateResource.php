<?php

namespace Modules\ExchangeRates\Filament\Resources;

use App\Models\Currency;
use Filament\Forms\Get;
use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;
use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\RelationManagers;
use Modules\ExchangeRates\Models\ExchangeRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }

    public static function getModelLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }

    public static function getPluralModelLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('exchangerates::rates.date'))
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label(__('exchangerates::rates.value'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('currency')
                    ->label(__('exchangerates::rates.currency'))
                    ->options(function (callable $get) {
                        // Get all currencies
                        $currencies = Currency::whereIn('id', setting('general.currencies'))->get();

                        // Get the selected base currency ID
                        $selectedBaseCurrencyId = $get('base_currency');

                        // Filter out the selected base currency from currency options
                        return $currencies->filter(function ($currency) use ($selectedBaseCurrencyId) {
                            return $currency->id != $selectedBaseCurrencyId;
                        })->pluck('code', 'id');
                    })
                    ->required()
                    ->reactive() // Makes this select react to changes
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // If currency matches base_currency, clear base_currency
                        if ($state === $get('base_currency')) {
                            $set('base_currency', null);
                        }
                    }),

                Forms\Components\Select::make('base_currency')
                    ->label(__('exchangerates::rates.base_currency'))
                    ->options(function (callable $get) {
                        // Get all currencies
                        $currencies = Currency::whereIn('id', setting('general.currencies'))->get();
                        $selectedCurrencyId = $get('currency');
                        return $currencies->filter(function ($currency) use ($selectedCurrencyId) {
                            return $currency->id != $selectedCurrencyId;
                        })->pluck('code', 'id');
                    })
                    ->default(function (callable $get) {
                        $defaultCurrencyId = Currency::find(setting('general.default_currency'))->id;
                        $selectedCurrencyId = $get('currency');
                        return $defaultCurrencyId != $selectedCurrencyId ? $defaultCurrencyId : null;
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($state === $get('currency')) {
                            $set('currency', null);
                        }
                    }),
                Forms\Components\TextInput::make('source')
                    ->label(__('exchangerates::rates.source'))
                    ->required()
                    ->maxLength(255)
                    ->default('NBP'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('exchangerates::rates.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('exchangerates::rates.value'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label(__('exchangerates::rates.currency'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_currency')
                    ->label(__('exchangerates::rates.base_currency'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('exchangerates::rates.source'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
