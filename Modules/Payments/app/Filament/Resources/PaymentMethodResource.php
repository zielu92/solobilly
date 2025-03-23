<?php

namespace Modules\Payments\Filament\Resources;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Payments\Models\PaymentMethodModel;
use Modules\Payments\Models\Transfer;
use Modules\Payments\PaymentMethodsManager;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethodModel::class;

    protected static ?string $navigationGroup = 'Settings';
    public static function getNavigationLabel(): string
    {
        return __('payments::payments.payment_methods');
    }

    public static function getModelLabel(): string
    {
        return __('payments::payments.payment_methods');
    }

    public static function getPluralModelLabel(): string
    {
        return __('payments::payments.payment_methods');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(PaymentMethodModel::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('payments::payments.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('payments::payments.description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('payments::payments.method'))
                    ->state(function ($record): string {
                        return __('payments::payments.methods.'.strtolower($record['method']));
                    })
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('payments::payments.active'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('setup')
                    ->visible(function ($record) {
                        return PaymentMethodsManager::canEdit(strtolower($record->method));
                    })
                    ->label(__('payments::payments.setup'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->url(function ($record) {
                        return PaymentMethodsManager::getEditCreateRoute(strtolower($record->method), $record);
                    })

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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
