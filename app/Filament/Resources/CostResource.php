<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostResource\Pages;
use App\Filament\Resources\CostResource\RelationManagers;
use App\Filament\Resources\CostResource\Widgets\CostsChartWidget;
use App\Models\Cost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostResource extends Resource
{
    protected static ?string $model = Cost::class;

    protected static ?string $navigationGroup = 'Costs';
    protected static ?string $navigationLabel = 'Costs';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Select::make('category_id')
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
                    ->maxLength(255)
                    ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                    ->default(null),

                Forms\Components\DatePicker::make('invoice_date')
                    ->visible(fn (callable $get) => !$get('is_category_tax_related')),

                Forms\Components\FileUpload::make('invoice_file_path')
                    ->imageEditor()
                    ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                    ->default(null),

                Forms\Components\FileUpload::make('receipt_file_path')
                    ->imageEditor()
                    ->default(null),

                Forms\Components\DatePicker::make('invoice_due_date')
                    ->visible(fn (callable $get) => !$get('is_category_tax_related')),
                Forms\Components\DatePicker::make('payment_date'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
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
