<?php

namespace Modules\Payments\Filament\Resources\TransferResource\Pages;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Modules\Payments\Filament\Resources\TransferResource;
use Filament\Resources\Pages\CreateRecord;
use Modules\Payments\Models\Transfer;

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

        return $form
            ->schema(
                array_merge(
                    Transfer::getForm(),
                    [
                        Hidden::make('payment_method_id')
                            ->required()
                            ->default($paymentMethodId),
                    ]
                )
            );
    }
}
