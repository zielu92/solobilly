<?php

namespace App\Filament\Pages;
use Chiiya\FilamentAccessControl\Traits\AuthorizesPageAccess;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;
class Backups extends BaseBackups
{
    use AuthorizesPageAccess;
    protected static string $view = 'filament.pages.backups';

    public static string $permission = 'backups.view';

    public function mount(): void
    {
        static::authorizePageAccess();
    }
}
