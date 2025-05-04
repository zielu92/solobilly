<?php

namespace Modules\ExchangeRates\Filament\Clusters;

use Filament\Clusters\Cluster;
use Nwidart\Modules\Facades\Module;

class ExchangeRates extends Cluster
{
    /**
     * Retrieves the module's name.
     *
     * This static method returns the hard-coded name identifier for the module.
     *
     * @return string The module name.
     */
    public static function getModuleName(): string
    {
        return 'ExchangeRates';
    }

    /**
     * Retrieves the module instance.
     *
     * Uses the module name from getModuleName() to fetch the corresponding module via Module::findOrFail().
     * An exception is thrown if no module is found with the given name.
     *
     * @return \Nwidart\Modules\Module The module instance.
     */
    public static function getModule(): \Nwidart\Modules\Module
    {
        return Module::findOrFail(static::getModuleName());
    }

    /**
     * Returns the localized navigation label for the Exchange Rates module.
     *
     * This method provides the label used in the module's navigation interface.
     *
     * @return string The navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('Exchange Rates');
    }

    /**
     * Returns the navigation icon for the Exchange Rates module.
     *
     * This method provides the icon identifier used for navigation in the Filament interface.
     *
     * @return string|null The navigation icon, always 'heroicon-o-squares-2x2'.
     */
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-squares-2x2';
    }
}
