<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuyerResource\Pages;
use App\Models\Buyer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuyerResource extends Resource
{
    protected static ?string $model = Buyer::class;

    public static function getNavigationLabel(): string
    {
        return __('buyers.buyers');
    }

    public static function getModelLabel(): string
    {
        return __('buyers.buyer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('buyers.buyers');
    }
    protected static ?string $recordTitleAttribute = 'company_name';
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Buyer::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('buyers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('buyers.company_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('buyers.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('buyers.phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label(__('buyers.tax_id'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListBuyers::route('/'),
            'create' => Pages\CreateBuyer::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
