<?php

namespace Modules\ExchangeRates\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'ExchangeRates';

    /**
     * Boot the service provider.
     *
     * This method is invoked before routes are registered, providing an opportunity to initialize model bindings
     * and pattern-based filters. Currently, it delegates its functionality to the parent boot method.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Register API and web routes for the ExchangeRates module.
     *
     * This method delegates the registration of routes by invoking the methods that
     * group and apply middleware to both API and web routes.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Registers the module's web routes.
     *
     * Loads the route definitions from the module's web.php file and groups them under the 'web' middleware,
     * ensuring that session state, CSRF protection, and other web-related features are active.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Register module-specific API routes.
     *
     * This method maps the API routes by applying the 'api' middleware, setting the URL
     * prefix to 'api', and the route name prefix to 'api.'. It groups the routes defined
     * in the module's "routes/api.php" file, ensuring that these routes remain stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }
}
