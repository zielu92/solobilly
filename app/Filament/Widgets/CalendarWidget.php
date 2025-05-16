<?php

namespace App\Filament\Widgets;


use App\Models\Buyer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\Widgets\CalendarWidget as CalendarWidgetBase;
use Illuminate\Database\Eloquent\Collection;
use Log;

class CalendarWidget extends CalendarWidgetBase
{
    protected static ?int $sort = 0;
    public function getOptions(): array
    {
        return [
            'nowIndicator' => true,
            'slotDuration' => '00:15:00'
        ];
    }

    protected bool $dateSelectEnabled = true;
    public function getEvents(array $fetchInfo = []): Collection | array
    {

        return [
            // Chainable object-oriented variant
            CalendarEvent::make()
                ->title('My first event')
                ->start(today())
                ->end(today()),

            // Array variant
            ['title' => 'My second event', 'start' => today()->addDays(3), 'end' => today()->addDays(3)],

            // Eloquent model implementing the `Eventable` interface
//            MyEvent::find(1),
        ];
    }

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Select::make('company_id')
                    ->label('Company')
                    ->options(Buyer::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DateTimePicker::make('start_time')
                    ->label('Start Time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('End Time')
                    ->required(),
            ]);
    }

    public function submit()
    {
        $data = $this->form->getState();

        // Save the event to the database or perform necessary actions

        // Optionally, add the event to the calendar
        $this->dispatch('refreshCalendar');
    }

}
