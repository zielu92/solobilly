<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //todo: fix currency, translations etc.
            Stat::make('Invoices Net', Invoice::sum('grand_total_net'))
            ->description('for '.date('Y'))
            ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
            ->chart([1,2,3,4])
            ->color('success')
        ];
    }
}
