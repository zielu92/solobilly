<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostResource\Pages;
use App\Models\Cost;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
            ->schema(Cost::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('costs.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_gross')
                    ->label(__('costs.amount_gross'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percent_deductible_from_taxes')
                    ->label(__('costs.percent_deductible_from_taxes'))
                    ->numeric()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state.'%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label(__('invoices.currency')),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('costs.cost_category'))
                    ->numeric()
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
        $unpaid =  Cost::where('payment_date', null)->count();
        return $unpaid > 0 ? $unpaid : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
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
