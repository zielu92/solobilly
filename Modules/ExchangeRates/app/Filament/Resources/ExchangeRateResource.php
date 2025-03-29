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

    /**
     * Returns the localized navigation label for the exchange rates resource.
     *
     * @return string The localized label for navigation.
     */
    public static function getNavigationLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }

    /**
     * Retrieves the localized singular label for the ExchangeRate model.
     *
     * @return string The singular model label.
     */
    public static function getModelLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }

    /**
     * Returns the localized plural label for the ExchangeRate model.
     *
     * This label is used in the admin panel to represent the plural form of exchange rates.
     *
     * @return string Localized plural label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('exchangerates::rates.exchange_rates');
    }
    /**
     * Configures and returns the form schema for exchange rate entries.
     *
     * The schema consists of:
     * - A required date picker for selecting the exchange rate date.
     * - A required numeric input for the exchange rate value.
     * - Reactive select inputs for currency and base currency that ensure the same currency is not selected for both fields.
     * - A required text input for the source with a maximum length of 255 characters, defaulting to "NBP".
     *
     * @param \Filament\Forms\Form $form The form instance to configure.
     *
     * @return \Filament\Forms\Form The form instance with the configured schema.
     */
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

    /**
     * Configures the table for displaying exchange rate records.
     *
     * Sets up a Filament table instance with columns for date, value, currency, base currency, and source,
     * each equipped with sorting, searching, and appropriate data formatting. Adds actions for editing
     * and deleting individual records, along with a bulk delete action group.
     *
     * @param Table $table The table instance to configure.
     *
     * @return Table The configured table instance.
     */
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

    /**
     * Returns the relationships defined for this resource.
     *
     * Currently, no relationships are specified.
     *
     * @return array An empty array indicating that no relationships exist.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Provides the routes for exchange rate resource pages.
     *
     * Returns an associative array mapping page identifiers to their corresponding route definitions:
     * - 'index': the route for listing exchange rate records.
     * - 'create': the route for creating a new exchange rate.
     * - 'edit': the dynamic route for editing an existing exchange rate (using the {record} identifier).
     *
     * @return array<string, mixed> An array of page route definitions.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
