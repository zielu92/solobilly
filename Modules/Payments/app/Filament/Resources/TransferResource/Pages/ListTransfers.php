<?php

namespace Modules\Payments\Filament\Resources\TransferResource\Pages;

use Modules\Payments\Filament\Resources\TransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
