<?php

namespace App\Filament\Widgets;

use App\Models\WorkLog;
use App\Traits\FilterTrait;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\Widgets\CalendarWidget as CalendarWidgetBase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class CalendarWidget extends CalendarWidgetBase
{
    use InteractsWithPageFilters, FilterTrait;

    protected static ?int $sort = 0;

    protected $listeners = [
        'filament.pageFilterUpdated' => 'handleFilterUpdated'
    ];

    public function getHeading(): string|HtmlString
    {
        return __("worklogs.worklog");
    }

    public function getOptions(): array
    {
        return [
            'nowIndicator' => true,
            'buttonText' => [
                "today"=> __('worklogs.today'),
            ]
        ];
    }

    public function handleFilterUpdated($data)
    {
        $this->refreshRecords();
    }

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        $startDate = $this->startDate();
        $endDate = $this->endDate();

        return WorkLog::with('buyer')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start', [$startDate, $endDate])
                    ->orWhereBetween('end', [$startDate, $endDate]);
            })
            ->get()
            ->map(function (WorkLog $workLog) {
                if (!is_null($workLog->unit_amount)) {
                    $amountDisplay = sprintf('%s %s', $workLog->unit_amount, $workLog->buyer->unit_type);
                    return CalendarEvent::make()
                        ->title(sprintf('%s (%s)', $workLog->buyer->name, $amountDisplay))
                        ->backgroundColor($workLog->buyer->color)
                        ->textColor(textColorContrast($workLog->buyer->color))
                        ->start(Carbon::parse($workLog->start)->format('Y-m-d'))
                        ->end(Carbon::parse($workLog->start)->format('Y-m-d'));
                } else {
                    $amountDisplay = sprintf('%s h', $workLog->duration);
                    return CalendarEvent::make()
                        ->title(sprintf('%s (%s)', $workLog->buyer->name, $amountDisplay))
                        ->start($workLog->start)
                        ->end($workLog->end)
                        ->backgroundColor($workLog->buyer->color)
                        ->textColor(textColorContrast($workLog->buyer->color));
                }

            })->toArray();
    }

}
