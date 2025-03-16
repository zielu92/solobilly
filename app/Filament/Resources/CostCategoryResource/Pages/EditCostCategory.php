<?php

namespace App\Filament\Resources\CostCategoryResource\Pages;

use App\Filament\Resources\CostCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCostCategory extends EditRecord
{
    protected static string $resource = CostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
