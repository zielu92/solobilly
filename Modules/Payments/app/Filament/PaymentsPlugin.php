<?php

namespace Modules\Payments\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PaymentsPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Payments';
    }

    public function getId(): string
    {
        return 'payments';
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
