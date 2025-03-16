<?php

namespace Modules\Payments\Filament\Resources\TransferResource\Pages;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Modules\Payments\Filament\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransfer extends EditRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('bankName')
                    ->required()
                    ->maxLength(255),
                TextInput::make('accountNumber')
                    ->required()
                    ->maxLength(255),
                TextInput::make('swift')
                    ->maxLength(255),
                TextInput::make('iban')
                    ->maxLength(255),
                TextInput::make('beneficiaryName')
                    ->maxLength(255),
                TextInput::make('beneficiaryAddress')
                    ->maxLength(255),
                Hidden::make('payment_method_id'),
            ]);
    }
}
