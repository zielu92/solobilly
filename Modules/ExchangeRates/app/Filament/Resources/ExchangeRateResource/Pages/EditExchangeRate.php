<?php

namespace Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;

use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExchangeRate extends EditRecord
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
