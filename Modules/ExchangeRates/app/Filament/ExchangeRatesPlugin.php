<?php

namespace Modules\ExchangeRates\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ExchangeRatesPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    /**
     * Returns the module name.
     *
     * This method always returns 'ExchangeRates', indicating the specific module.
     *
     * @return string The module name.
     */
    public function getModuleName(): string
    {
        return 'ExchangeRates';
    }

    /**
     * Returns the unique identifier for this plugin.
     *
     * @return string The plugin ID.
     */
    public function getId(): string
    {
        return 'exchangerates';
    }

    /**
     * Initializes the plugin with the provided panel.
     *
     * This method is responsible for bootstrapping the plugin components using the given panel instance.
     * The detailed implementation is pending.
     *
     * @param Panel $panel The panel instance for initializing the plugin.
     */
    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
