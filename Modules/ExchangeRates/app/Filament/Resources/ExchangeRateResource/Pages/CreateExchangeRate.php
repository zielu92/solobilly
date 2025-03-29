<?php

namespace Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;

use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'manual';

        return $data;
    }
}
