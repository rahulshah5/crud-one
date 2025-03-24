<?php

namespace AlphaTechnologies\CRUDZen;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use AlphaTechnologies\CRUDZen\Livewire\Table;

class CRUDZenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register Livewire components
        Livewire::component('table', Table::class);

        // Publish views and config
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views'),
            __DIR__ . '/../src/Livewire' => app_path('Livewire'),
            __DIR__ . '/../src/Models' => app_path('Models'),
            __DIR__ . '/../src/Controller' => app_path('Http/Controllers'),
            __DIR__ . '/../migration' => database_path('migrations'),
        ], 'crudzen');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'crudzen');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../migration');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/crudzen.php', 'crudzen'
        );
    }

    public function register()
    {
        $this->app->singleton('crudzen', function ($app) {
            return new CRUDZen;
        });
    }
}

