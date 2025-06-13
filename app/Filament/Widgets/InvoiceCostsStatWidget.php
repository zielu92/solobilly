<?php

namespace App\Filament\Widgets;

use App\Models\Cost;
use App\Models\Currency;
use App\Models\Invoice;
use App\Traits\FilterTrait;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\ExchangeRates\Models\ExchangeRate;

class InvoiceCostsStatWidget extends BaseWidget
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

        // Get Invoices
        $invoices = Invoice::select('grand_total_net', 'grand_total_gross', 'created_at', 'currency_id')
            ->where('payment_status', $status)
            ->where('type', 'regular')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->get();

        $totalValue = 0;
        $totalValues = [];

        foreach ($invoices as $invoice) {
            $grandTotal = $type === 'net' ? $invoice->grand_total_net : $invoice->grand_total_gross;
            $grandTotal = $this->convertToDefaultCurrency($grandTotal, $invoice->currency_id, $invoice->created_at);

            $totalValue += $grandTotal;
            $totalValues[] = $totalValue;
        }

        // Get Costs (non-tax related)
        $costs = $this->getCosts(false, $startDate, $endDate, $status);

        $totalCost = 0;
        $totalCosts = [];

        foreach ($costs as $cost) {
            $grandTotal = $type === 'net' ? $cost->afterTaxDeductibleCostNet : $cost->afterTaxDeductibleCostGross;
            $grandTotal = $this->convertToDefaultCurrency($grandTotal, $cost->currency_id, $cost->created_at);

            $totalCost += $grandTotal;
            $totalCosts[] = $totalCost;
        }

        // Get Taxes (tax-related costs)
        $taxes = $this->getCosts(true, $startDate, $endDate, $status);

        $totalTax = 0;
        $totalTaxes = [];

        foreach ($taxes as $tax) {
            $grandTotal = $type === 'net' ? $tax->amount : $tax->amount_gross;
            $grandTotal = $this->convertToDefaultCurrency($grandTotal, $tax->currency_id, $tax->created_at);

            $totalTax += $grandTotal;
            $totalTaxes[] = $totalTax;
        }

        return [
            Stat::make(__('dashboard.est_invoice_net'), $this->formatAmount($totalValue) . ' ' . $defaultCurrency->code)
                ->description($startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'))
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart($totalValues)
                ->color('success'),

            Stat::make(__('dashboard.est_costs_net'), $this->formatAmount($totalCost) . ' ' . $defaultCurrency->code)
                ->description($startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'))
                ->descriptionIcon('heroicon-o-credit-card', IconPosition::Before)
                ->chart($totalCosts)
                ->color('warning'),

            Stat::make(__('dashboard.est_tax_value'), $this->formatAmount($totalTax) . ' ' . $defaultCurrency->code)
                ->description($startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'))
                ->descriptionIcon('heroicon-o-building-office', IconPosition::Before)
                ->chart($totalTaxes)
                ->color('danger')
        ];
    }

    private function convertToDefaultCurrency($amount, $currencyId, $createdAt): float|int
    {
        $defaultCurrencyId = setting('general.default_currency');

        if ($currencyId === $defaultCurrencyId) {
            return $amount;
        }

        $defaultCurrencyId = setting('general.default_currency');
        $exchangeRate = ExchangeRate::where('currency_id', $currencyId)
            ->where('base_currency_id', $defaultCurrencyId)
            ->whereDate('date', '<=', Carbon::parse($createdAt)->startOfDay())
            ->orderBy('date', 'desc')
            ->first();

        return $exchangeRate ? $amount * $exchangeRate->value : $amount;
    }

    private function getCosts(bool $isTaxRelated, $startDate, $endDate, $status)
    {
        return Cost::select('amount_gross', 'amount', 'created_at', 'currency_id', 'percent_deductible_from_taxes')
            ->when($status === 'paid', function ($query) {
                $query->whereNotNull('payment_date');
            }, function ($query) {
                $query->whereNull('payment_date');
            })
            ->whereHas('category', function ($query) use ($isTaxRelated) {
                $query->where('is_tax_related', $isTaxRelated);
            })
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get();
    }

    private function formatAmount($amount): string
    {
       return number_format($amount, 2, ',', ' ');
    }
}
