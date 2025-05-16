<?php

namespace App\Filament\Resources\WorkLogResource\Pages;

use App\Filament\Resources\WorkLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkLog extends CreateRecord
{
    protected static string $resource = WorkLogResource::class;
}
