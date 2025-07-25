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

    private $year;
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
        'Sweden',
        'Spain',
        'Switzerland'
    ];

    protected static bool $isLazy = false;

    protected $listeners = [
        'refreshHolidays' => 'loadHolidays',
        'filament.pageFilterUpdated' => 'handleFilterUpdated',
        'onCountryChanged' => 'loadHolidays',
    ];

    /**
     * Initializes the widget state with the selected year and default country, then loads holiday data.
     *
     * Sets the year based on the 'startDate' filter if available, defaults to the current year otherwise, assigns 'Poland' as the default country, and populates the holidays list.
     */
    public function mount()
    {
        $this->loadYear();
        $this->country = 'Poland';
        $this->loadHolidays();
    }

    /**
     * Reloads the list of holidays when the selected country changes.
     */
    public function updatedCountry($value)
    {
        $this->loadYear();
        $this->country = $value;
        $this->loadHolidays();
        $this->dispatch('refreshComponent');
    }

    private function loadYear() {
        $startDate = $this->filters['startDate'] ?? null;
        $this->year = $startDate ? Carbon::parse($startDate)->format('Y') : now()->year;
    }

    /**
     * Updates the displayed holidays when page filters change.
     *
     * If the provided data includes a 'startDate', updates the year accordingly and reloads the holiday list.
     *
     * @param mixed $data Filter data, expected to be an array with an optional 'startDate' key.
     */
    public function handleFilterUpdated($data)
    {
        if (is_array($data) && isset($data['startDate'])) {
            $this->year = $data['startDate'] ? Carbon::parse($data['startDate'])->format('Y') : now()->year;
        }
        $this->loadHolidays();
    }

    /**
     * Loads and formats holidays for the selected country and year.
     *
     * Retrieves holidays using the Yasumi library, formats each with its name, date, and localized day of the week, and stores them in the holidays property. If an error occurs during retrieval, logs the error and clears the holidays list.
     */
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
