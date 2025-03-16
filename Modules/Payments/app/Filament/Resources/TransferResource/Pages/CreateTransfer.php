<?php

namespace Modules\Payments\Filament\Resources\TransferResource\Pages;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Modules\Payments\Filament\Resources\TransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    public function form(Form $form): Form
    {
        $paymentMethodId = request()->get('payment_method_id');
        if(!is_numeric($paymentMethodId)) {
            abort(403);
        }
        return $form
            ->schema([
                TextInput::make('bankName')
                    ->required()
                    ->maxLength(255),
                TextInput::make('accountNumber')
                    ->required()
                    ->maxLength(255),
                TextInput::make('swift')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('iban')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('beneficiaryName')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('beneficiaryAddress')
                    ->maxLength(255)
                    ->default(null),
                Hidden::make('payment_method_id')
                    ->default($paymentMethodId),
            ]);
    }
}
