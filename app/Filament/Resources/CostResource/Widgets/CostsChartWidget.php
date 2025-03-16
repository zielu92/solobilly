<?php

namespace App\Filament\Resources\CostResource\Widgets;

use App\Filament\Resources\CostResource\Pages\ListCosts;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CostsChartWidget extends ChartWidget
{
    use InteractsWithPageTable;

    protected static ?string $heading = 'Costs sum';

    protected  int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = "200px";
    protected static ?string $pollingInterval = '1s';

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last week',
            'month' => 'this month',
            '1Year' => 'This year'
        ];
    }

    protected function getTablePage(): string
    {
        return ListCosts::class;
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        $data = match ($filter) {
            'week' => Trend::query($this->getPageTableQuery())
                ->between(
                    start: now()->subWeek(),
                    end: now(),
                )
                ->perDay()
                ->sum('amount'),
            'month' => Trend::query($this->getPageTableQuery())
                ->between(
                    start: now()->startOfMonth(),
                    end: now(),
                )
                ->perDay()
                ->sum('amount'),
            '1Year' => Trend::query($this->getPageTableQuery())
                ->between(
                    start: now()->startOfYear(),
                    end: now(),
                )
                ->perMonth()
                ->sum('amount')
        };

        return [
            'datasets' => [
                [
                    'label' => 'PLN',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981', // Green color for the line
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)', // Light green with opacity
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
