<?php

namespace Modules\ExchangeRates\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\ExchangeRates\Console\CheckExchangeRates;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ExchangeRatesServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'ExchangeRates';

    protected string $nameLower = 'exchangerates';

    /**
     * Boot the service provider.
     *
     * This method initializes the module by registering its commands, scheduled tasks, translations, configuration,
     * views, and database migrations.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Registers module-dependent service providers for the Exchange Rates module.
     *
     * This method registers the EventServiceProvider and RouteServiceProvider, ensuring that the module's event listeners and routing configurations are initialized during the application's boot process.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Registers the ExchangeRates module's console commands.
     *
     * This method registers the CheckExchangeRates command, enabling its execution via the application's CLI.
     */
    protected function registerCommands(): void
    {
         $this->commands([
             CheckExchangeRates::class
         ]);
    }

    /**
     * Schedules the 'exchange-rates:check' command to run daily at 9:00 AM.
     *
     * This method registers a callback to add the command to the application's scheduler once the application has booted.
     */
    protected function registerCommandSchedules(): void
    {
         $this->app->booted(function () {
             $schedule = $this->app->make(Schedule::class);
             $schedule->command('exchange-rates:check')->dailyAt('9:00');
         });
    }

    /**
     * Registers the module's translation files.
     *
     * This method checks for a custom translation directory within the application's
     * resources. If the directory exists, it loads both standard and JSON translations
     * from there. Otherwise, it falls back to the module's default language files.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Registers and publishes the module's configuration files.
     *
     * This method locates PHP configuration files from the module's configuration directory,
     * publishes each file to the application's configuration path, and merges its settings
     * into the application's configuration repository using a namespaced key derived from
     * the module's lowercase name. The primary configuration file ('config.php') is handled
     * with a simplified key.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Registers the module's view templates and Blade component namespace.
     *
     * This method publishes the module's views from its resources directory to the application's view path,
     * loads views from both the published and original module source paths, and sets up a corresponding Blade
     * component namespace to facilitate the use of module-specific Blade components.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Returns an empty array, indicating that this provider does not bind any services.
     *
     * @return array An empty array.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Retrieves the list of publishable view paths for the module.
     *
     * Iterates over the application's configured view paths and collects any directory
     * that matches the module's designated view directory (based on its lowercase name).
     *
     * @return array An array of valid paths where the module's view files are located.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
