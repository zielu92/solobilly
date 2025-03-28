<?php

namespace Modules\ExchangeRates\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ExchangeRatesPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'ExchangeRates';
    }

    public function getId(): string
    {
        return 'exchangerates';
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
