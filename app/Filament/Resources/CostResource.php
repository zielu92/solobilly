<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostResource\Pages;
use App\Filament\Resources\CostResource\RelationManagers;
use App\Filament\Resources\CostResource\Widgets\CostsChartWidget;
use App\Models\Cost;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostResource extends Resource
{
    protected static ?string $model = Cost::class;

    public static function getNavigationLabel(): string
    {
        return __('costs.costs');
    }

    public static function getModelLabel(): string
    {
        return __('costs.cost');
    }

    public static function getPluralModelLabel(): string
    {
        return __('costs.costs');
    }
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('costs.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('amount')
                    ->label(__('costs.amount'))
                    ->required()
                    ->live()
                    ->numeric()
                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                    $set('amount_gross', is_numeric($get('amount')) ? $get('amount')*1.23 : 0)
                    ),
                Select::make('currency_id')
                    ->label(__('invoices.currency'))
                    ->columnSpan(1)
                    ->live()
                    ->options(
                        Currency::whereIn('id', (array) setting('general.currencies', []))
                            ->pluck('code', 'id')
                    )
                    ->default(Currency::find(setting('general.default_currency'))->id)
                    ->required(),
                Forms\Components\TextInput::make('amount_gross')
                    ->label(__('costs.amount_gross'))
                    ->required()
                    ->live()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->label(__('costs.description'))
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date')
                    ->label(__('costs.date'))
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label(__('costs.category'))
                    ->live()
                    ->relationship('category', 'name')
                    ->required()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Store the is_tax_related value in a hidden field for reference
                        if ($state) {
                            $isTaxRelated = \App\Models\CostCategory::find($state)?->is_tax_related ?? true;
                            $set('is_category_tax_related', $isTaxRelated);
                        } else {
                            $set('is_category_tax_related', true);
                        }
                    }),
                Forms\Components\Hidden::make('is_category_tax_related')
                    ->default(true),

                Forms\Components\TextInput::make('invoice_number')
                    ->label(__('costs.invoice_number'))
                    ->maxLength(255)
                    ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                    ->default(null),

                Forms\Components\DatePicker::make('invoice_date')
                    ->label(__('costs.invoice_date'))
                    ->visible(fn (callable $get) => !$get('is_category_tax_related')),

                Forms\Components\FileUpload::make('invoice_file_path')
                    ->label(__('costs.invoice_file'))
                    ->imageEditor()
                    ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                    ->default(null),

                Forms\Components\FileUpload::make('receipt_file_path')
                    ->label(__('costs.file_path'))
                    ->imageEditor()
                    ->default(null),

                Forms\Components\DatePicker::make('invoice_due_date')
                    ->label(__('costs.invoice_due'))
                    ->visible(fn (callable $get) => !$get('is_category_tax_related')),
                Forms\Components\DatePicker::make('payment_date')
                    ->label(__('costs.payment_date')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('costs.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('costs.amount'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_gross')
                    ->label(__('costs.amount_gross'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('costs.cost_category'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('costs.invoice_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_due_date')
                    ->label(__('costs.invoice_due'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('costs.payment_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            CostsChartWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosts::route('/'),
            'create' => Pages\CreateCost::route('/create'),
            'edit' => Pages\EditCost::route('/{record}/edit'),
        ];
    }
}
