<?php

namespace Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Payments\Filament\Resources\PaymentMethodResource;
use Modules\Payments\PaymentMethodsManager;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();
        $redirect = PaymentMethodsManager::getEditCreateRoute($record->method, $record);
        if($redirect) {
            return $redirect;
        }
        return parent::getRedirectUrl();
    }
}
