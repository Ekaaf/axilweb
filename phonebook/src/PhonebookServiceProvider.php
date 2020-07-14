<?php

namespace Axilweb\Phonebook;

use Illuminate\Support\ServiceProvider;

class PhonebookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Axilweb\Phonebook\PhonebookController');
        $this->loadViewsFrom(__DIR__.'/views', 'phonebook');
        $this->app->bind('Phonebook', function ($app) {
          return new Phonebook();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadFactoriesFrom(__DIR__.'/database/factories');
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/../database/seeds/' => database_path('seeds')
        ], 'seeds');
        $this->publishes([
            __DIR__.'/../database/factories/' => database_path('factories')
        ], 'factories');
        $this->publishes([
            __DIR__.'/../database/model/' => app_path()
        ], 'model');
        include __DIR__.'/routes.php';
    }
}
