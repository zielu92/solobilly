<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Cost;
use App\Models\Currency;
use App\Models\Invoice;
use App\Traits\FilterTrait;
use Carbon\Carbon;
use DB;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\ExchangeRates\Models\ExchangeRate;

class InvoicesWidget extends BaseWidget
{
    use InteractsWithPageFilters, FilterTrait;
    protected function getStats(): array
    {
        $startDate = $this->startDate();
        $endDate = $this->endDate();
        $type = $this->type();
        $status = $this->status();

        $defaultCurrencyId = setting('general.default_currency');
        $defaultCurrency = Currency::find($defaultCurrencyId);

        $invoices = Invoice::select('grand_total_net', 'grand_total_gross', 'created_at', 'currency_id')
            ->where('payment_status', $status)
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->get();

        $totalValue = 0;
        $totalValues = [];

        foreach ($invoices as $invoice) {
            $grandTotal = $type==='net' ? $invoice->grand_total_net : $invoice->grand_total_gross;

            if ($invoice->currency_id !== $defaultCurrencyId) {
                $invoiceDate = Carbon::parse($invoice->created_at)->startOfDay();

                $exchangeRate = ExchangeRate::where('currency', $invoice->currency_id)
                    ->whereDate('date', '<=', $invoiceDate)
                    ->orderBy('date', 'desc')
                    ->first();

                if ($exchangeRate) {
                    $grandTotal *= $exchangeRate->value;
                }
            }

            $totalValue += $grandTotal;
            $totalValues[] = $totalValue;
        }
        //todo: where cat is not related to tax
        $costs = Cost::select('amount_gross','amount', 'created_at', 'currency_id')
            ->where('payment_date', $status=='paid' ? '!=' : '=', null)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get();

        $totalCost = 0;
        $totalCosts = [];

        foreach ($costs as $cost) {
            $grandTotal = $type==='net' ? $cost->amount : $cost->amount_gross;

            if ($cost->currency_id !== $defaultCurrencyId) {
                $createdDate = Carbon::parse($cost->created_at)->startOfDay();

                $exchangeRate = ExchangeRate::where('currency', $cost->currency_id)
                    ->whereDate('date', '<=', $createdDate)
                    ->orderBy('date', 'desc')
                    ->first();

                if ($exchangeRate) {
                    $grandTotal *= $exchangeRate->value;
                }
            }

            $totalCost += $grandTotal;
            $totalCosts[] = $totalValue;
        }


        return [
            Stat::make(__('dashboard.est_invoice_net'), $totalValue . ' ' . $defaultCurrency->code)
                ->description($startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'))
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart($totalValues)
                ->color('success'),
            Stat::make(__('dashboard.est_costs_net'), $totalCost . ' ' . $defaultCurrency->code)
                ->description($startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'))
                ->descriptionIcon('heroicon-o-credit-card', IconPosition::Before)
                ->chart($totalCosts)
                ->color('warning')
        ];
    }
}
