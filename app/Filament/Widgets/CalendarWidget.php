<?php

namespace App\Filament\Widgets;

use App\Models\WorkLog;
use App\Traits\FilterTrait;
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
                return CalendarEvent::make()
                    ->title(sprintf('%s (%s h)', $workLog->buyer->name, $workLog->duration))
                    ->start($workLog->start)
                    ->end($workLog->end)
                    ->backgroundColor($workLog->buyer->color)
                    ->textColor(textColorContrast($workLog->buyer->color));
            })->toArray();
    }

}
