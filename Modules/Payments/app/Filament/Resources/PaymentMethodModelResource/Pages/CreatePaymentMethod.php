<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource;
use Filament\Resources\Pages\CreateRecord;
use Modules\Payments\Filament\Resources\PaymentMethodResource;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
