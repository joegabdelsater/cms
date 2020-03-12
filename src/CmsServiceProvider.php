<?php

namespace Xtnd\Cms;
// namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/views', 'cms');

   
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
        $this->app->make('Xtnd\Cms\CmsController');

    }
}
