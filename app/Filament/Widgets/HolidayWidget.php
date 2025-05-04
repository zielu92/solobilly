<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Log;
use Yasumi\Yasumi;

class HolidayWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected static string $view = 'filament.widgets.holiday-widget';

    public $year;
    public $country;
    public $holidays = [];
    public $countries = [
        'Poland',
        'Germany',
        'USA',
        'UnitedKingdom',
        'France',
        'Italy',
        'Netherlands',
        'Sweden'
    ];

    protected static bool $isLazy = false;

    protected $listeners = [
        'refreshHolidays' => 'loadHolidays',
        'filament.pageFilterUpdated' => 'handleFilterUpdated'
    ];

    public function mount()
    {
        // Safely access filters
        $startDate = $this->filters['startDate'] ?? null;
        $this->year = $startDate ? Carbon::parse($startDate)->format('Y') : now()->year;
        $this->country = 'Poland';
        $this->loadHolidays();
    }

    public function updatedCountry()
    {
        $this->loadHolidays();
    }

    public function handleFilterUpdated($data)
    {
        if (is_array($data) && isset($data['startDate'])) {
            $this->year = $data['startDate'] ? Carbon::parse($data['startDate'])->format('Y') : now()->year;
        }
        $this->loadHolidays();
    }

    public function loadHolidays()
    {
        try {
            $provider = Yasumi::create($this->country, (int)$this->year, app()->getLocale());
            $allHolidays = $provider->getHolidays();
            $this->holidays = [];
            foreach ($allHolidays as $holiday) {
                $holidayDate = Carbon::parse($holiday->format('Y-m-d'));
                $this->holidays[] = [
                    'name' => $holiday->getName(),
                    'date' => $holiday->format('Y-m-d'),
                    'day_of_week' => $holidayDate->translatedFormat('l'),
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Holiday widget error: ' . $e->getMessage());
            $this->holidays = [];
        }
    }
}
