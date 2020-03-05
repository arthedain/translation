<?php

namespace Arthedain\Translation;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Arthedain\Translation\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/database/migrations/2020_03_04_110252_create_translations_table.php' => app_path('../database/migrations/2020_03_04_110252_create_translations_table.php'),
        ], 'migration');
        $this->publishes([
            __DIR__.'/Models/Translation.php' => app_path('/Models/Translation.php'),
        ], 'model');
        $this->publishes([
            __DIR__.'/config/nova-translation.php' => app_path('../config/nova-translation.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'translation');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/translation')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
