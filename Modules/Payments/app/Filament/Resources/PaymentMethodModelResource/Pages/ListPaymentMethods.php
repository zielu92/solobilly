<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Payments\Filament\Resources\PaymentMethodResource;

class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
