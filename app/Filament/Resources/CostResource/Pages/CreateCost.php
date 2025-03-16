<?php

namespace App\Filament\Resources\CostResource\Pages;

use App\Filament\Resources\CostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCost extends CreateRecord
{
    protected static string $resource = CostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
