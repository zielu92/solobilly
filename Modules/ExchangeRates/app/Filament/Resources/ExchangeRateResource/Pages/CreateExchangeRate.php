<?php

namespace Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;

use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    /**
     * Modifies the input form data for creating a new exchange rate record.
     *
     * This method ensures that the exchange rate is marked as manually created by setting the 'type'
     * field of the data array to 'manual'.
     *
     * @param array $data The initial form data before record creation.
     * @return array The modified form data with the 'type' set to 'manual'.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'manual';

        return $data;
    }
}
