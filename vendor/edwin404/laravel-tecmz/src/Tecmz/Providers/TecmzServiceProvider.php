<?php

namespace Edwin404\Tecmz\Providers;

use Illuminate\Support\ServiceProvider;

class TecmzServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'tecmz');
        $this->publishes([
            __DIR__ . '/../../../config/tecmz.php' => config_path('tecmz.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/tecmz.php', 'tecmz'
        );
    }

}
