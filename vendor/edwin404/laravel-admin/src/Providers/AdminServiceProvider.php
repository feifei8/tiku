<?php

namespace Edwin404\Admin\Providers;

use Edwin404\Admin\Services\AdminUserService;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'admin');
        $this->publishes([
            __DIR__ . '/../../config/admin.php' => config_path('data.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/admin.php', 'admin'
        );

        $this->app->singleton('adminUserService', AdminUserService::class);
    }
}
