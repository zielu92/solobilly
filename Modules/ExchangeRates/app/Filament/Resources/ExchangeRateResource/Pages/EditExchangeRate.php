<?php

namespace Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;

use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExchangeRate extends EditRecord
{
    protected static string $resource = ExchangeRateResource::class;

    /**
     * Returns header actions for the exchange rate edit page.
     *
     * This method provides an array of actions to be displayed in the header, currently including a delete action to facilitate the removal of the exchange rate record.
     *
     * @return array List of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
