<?php

namespace Modules\Payments\Filament\Resources\TransferResource\Pages;

use Filament\Forms\Form;
use Modules\Payments\Filament\Resources\TransferResource;
use Filament\Resources\Pages\EditRecord;
use Modules\Payments\Models\Transfer;

class EditTransfer extends EditRecord
{
    protected static string $resource = TransferResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema(Transfer::getForm());
    }
}
