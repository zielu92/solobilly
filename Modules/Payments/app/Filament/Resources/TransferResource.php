<?php

namespace Modules\Payments\Filament\Resources;

use Modules\Payments\Filament\Resources\TransferResource\Pages;
use Modules\Payments\Models\Transfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('accountNumber')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bankName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('swift')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('beneficiaryName')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('beneficiaryAddress')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('accountNumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bankName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('iban')
                    ->searchable(),
                Tables\Columns\TextColumn::make('swift')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }
}
