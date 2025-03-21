<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostCategoryResource\Pages;
use App\Filament\Resources\CostCategoryResource\RelationManagers;
use App\Models\CostCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostCategoryResource extends Resource
{
    protected static ?string $model = CostCategory::class;

    public static function getNavigationLabel(): string
    {
        return __('costs.cost_categories');
    }

    public static function getModelLabel(): string
    {
        return __('costs.cost_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('costs.cost_categories');
    }
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('costs.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('costs.color'))
                    ->default(null),
                Forms\Components\Textarea::make('description')
                    ->label(__('costs.description'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_tax_related')
                    ->label(__('costs.tax_related'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('costs.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label(__('costs.color'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_tax_related')
                    ->label(__('costs.tax_related'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCostCategories::route('/'),
            'create' => Pages\CreateCostCategory::route('/create'),
        ];
    }
}
