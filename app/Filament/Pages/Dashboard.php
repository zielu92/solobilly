<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm, HasFiltersAction;

    /**
     * Builds and returns a filter form with start and end date pickers for the dashboard.
     *
     * The form includes two date pickers: one for the start date (defaulting to the first day of the current month, with a maximum of yesterday) and one for the end date (defaulting to today, with a minimum of the selected start date and a maximum of today). Both fields trigger a filter update event when changed.
     *
     * @param Form $form The form instance to configure.
     * @return Form The configured form schema with date filters.
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->default(now()->startOfMonth())
                            ->label(__('dashboard.start_date'))
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()->subDay())
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->dispatchFiltersUpdated();
                            }),
                        DatePicker::make('endDate')
                            ->default(now())
                            ->label(__('dashboard.end_date'))
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->minDate(fn(Get $get) => $get('startDate'))
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->dispatchFiltersUpdated();
                            }),
                        ToggleButtons::make('type')
                            ->label(__('dashboard.type'))
                            ->inline()
                            ->grouped()
                            ->options([
                                'gross' => __('dashboard.gross'),
                                'net'   => __('dashboard.net'),
                            ])
                            ->default('net')
                            ->afterStateUpdated(function () {
                                $this->dispatchFiltersUpdated();
                            }),
                        ToggleButtons::make('status')
                            ->label(__('dashboard.status'))
                            ->inline()
                            ->grouped()
                            ->colors([
                                'paid' => 'success',
                                'not_paid' => 'danger',
                            ])
                            ->options([
                                'paid' => __('dashboard.paid'),
                                'not_paid'   => __('dashboard.not_paid'),
                            ])
                            ->default('paid')
                            ->afterStateUpdated(function () {
                                $this->dispatchFiltersUpdated();
                            }),
                    ])
                    ->columns(4),
            ]);
    }

    /**
     * Dispatches a page filter updated event with the current filter values.
     *
     * Triggers the 'filament.pageFilterUpdated' event, passing the current filters as event data.
     */
    protected function dispatchFiltersUpdated(): void
    {
        $this->dispatch('filament.pageFilterUpdated', data: $this->filters);
    }

//    protected function getHeaderActions(): array
//    {
//        return [
//            FilterAction::make()
//                ->form([
//                    DatePicker::make('startDate')
//                        ->default(function () {
//                            return now()->startOfMonth()->format('Y-m-d');
//                        })
//                        ->label(__('dashboard.start_date'))
//                        ->displayFormat('d/m/Y')
//                        ->maxDate(now()->subDay())
//                        ->afterStateUpdated(fn(Get $get) =>   $this->dispatch('filament.pageFilterUpdated', data: $get))
//                        ->live(),
//                    DatePicker::make('endDate')
//                        ->default(function () {
//                            return now()->format('Y-m-d');
//                        })
//                        ->label(__('dashboard.end_date'))
//                        ->displayFormat('d/m/Y')
//                        ->maxDate(now())
//                        ->minDate(fn(Get $get) => $get('startDate'))
//                        ->live()
//                ])
//                ->action(function (array $data): void {
//                    $this->dispatch('filament.pageFilterUpdated', data: $data);
//                })
//        ];
//    }
}
