<?php

namespace Modules\ExchangeRates\Filament\Widgets;

use App\Models\Currency;
use App\Traits\FilterTrait;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\ExchangeRates\Models\ExchangeRate;

class ExchangeRatesWidget extends BaseWidget
{
    use InteractsWithPageFilters, FilterTrait;

    protected static ?int $sort = 1;
   protected function getTableHeading(): string|Htmlable|null
   {
       return  __('exchangerates::rates.exchange_rates');
   }

    protected function getTableQuery(): Builder|Relation|null
    {
        $startDate = $this->startDate();
        $endDate = $this->endDate();

        return ExchangeRate::query()->whereBetween('date', [$startDate, $endDate]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('date')
                ->label(__('exchangerates::rates.date'))
                ->date()
                ->sortable(),
            TextColumn::make('value')
                ->label(__('exchangerates::rates.value'))
                ->numeric()
                ->sortable(),
            TextColumn::make('currency.code')
                ->label(__('exchangerates::rates.currency'))
                ->sortable(),
            TextColumn::make('baseCurrency.code')
                ->label(__('exchangerates::rates.base_currency')),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('currency_id')
                ->options(fn() => Currency::whereIn('id', setting('general.currencies'))->pluck('code', 'id')->toArray())
                ->label(__('exchangerates::rates.currency')),
            SelectFilter::make('base_currency_id')
                ->options(fn() => Currency::whereIn('id', setting('general.currencies'))->pluck('code', 'id')->toArray())
                ->label(__('exchangerates::rates.base_currency')),
        ];
    }

}
