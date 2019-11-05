<?php

namespace App\Providers;

use Edwin404\Tecmz\Traits\AssetsCDNTrait;
use Edwin404\Tecmz\Traits\PayConfigTrait;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use AssetsCDNTrait;
    use PayConfigTrait;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        if (env('APP_DEBUG', false)) {
            //$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        $this->bootAssetsCDN();
        $this->bootPayConfig();

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
