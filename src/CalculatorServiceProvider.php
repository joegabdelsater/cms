<?php

namespace Devdojo\Calculator;
// namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class CalculatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'calculator');

   
        $this->publishes([
            __DIR__.'/views/plugins' => public_path('vendor/xtnd/cms/plugins'),
            __DIR__.'/views/dist' => public_path('vendor/xtnd/cms/dist'),

        ], 'public');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Devdojo\Calculator\CalculatorController');

    }
}
