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

    /**
     * Returns header actions for the exchange rates listing page.
     *
     * This method provides an array of actions to be displayed in the page header. It includes a custom
     * "fetch" action that requires user confirmation. When confirmed, it triggers an Artisan command to update
     * exchange rates and sends a success notification. Additionally, a create action is provided to enable the
     * addition of new exchange rate records.
     *
     * @return array The configured header actions.
     */
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
