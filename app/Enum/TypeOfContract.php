<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum TypeOfContract: string implements HasLabel
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return __(sprintf('buyers.type.%s',$this->value));
    }
}
