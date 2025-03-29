<?php

namespace Modules\ExchangeRates\Filament\Resources\ExchangeRateResource\Pages;


use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Modules\ExchangeRates\Filament\Resources\ExchangeRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('fetch')
                ->color('success')
                ->label(__('exchangerates::rates.fetch'))
                ->icon('heroicon-m-arrow-down-circle')
                ->requiresConfirmation()
                ->action(function ($livewire) {
                    Artisan::call('exchange-rates:check');
                    Notification::make()
                        ->title(__('exchangerates::rates.notification_fetch'))
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
