<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource;
use Filament\Resources\Pages\CreateRecord;
use Modules\Payments\Filament\Resources\PaymentMethodResource;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;
}
