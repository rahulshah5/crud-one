<?php

namespace RahulShah\CRUDOne;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use RahulShah\CRUDOne\Livewire\Table;

class CRUDOneServiceProvider extends ServiceProvider
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
        ], 'crudone');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'crudone');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../migration');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/crudone.php', 'crudone'
        );
    }

    public function register()
    {
        $this->app->singleton('crudone', function ($app) {
            return new CRUDOne;
        });
    }
}

