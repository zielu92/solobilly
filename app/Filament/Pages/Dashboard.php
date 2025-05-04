<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm, HasFiltersAction;

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
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Explicitly dispatch the filters updated event with current filters
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
