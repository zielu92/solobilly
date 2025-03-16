<?php

namespace Modules\Payments\Filament\Clusters;

use Filament\Clusters\Cluster;
use Nwidart\Modules\Facades\Module;

class Payments extends Cluster
{
    public static function getModuleName(): string
    {
        return 'Payments';
    }

    public static function getModule(): \Nwidart\Modules\Module
    {
        return Module::findOrFail(static::getModuleName());
    }

    public static function getNavigationLabel(): string
    {
        return __('Payments');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-squares-2x2';
    }
}
