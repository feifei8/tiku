<?php

namespace Edwin404\Base\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'base');
    }

    public function register()
    {
        $this->setupMonitor();
    }

    private function setupMonitor()
    {
        static $queryCountPerRequest = 0;

        Route::after(function () use (&$queryCountPerRequest) {
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $param = json_encode(Request::input());
            $url = Request::url();
            $method = Request::getMethod();
            if ($time > 200) {
                $param = json_encode(Request::input());
                $url = Request::url();
                $method = Request::getMethod();
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            if ($queryCountPerRequest > 10) {
                Log::warning("MASS_REQUEST_SQL $queryCountPerRequest $method [$url] $param");
            }
        });

        Event::listen('illuminate.query', function ($query, $bindings, $time, $connectionName) use (&$queryCountPerRequest) {
            $queryCountPerRequest++;
            if ($time > 50) {
                $param = json_encode($bindings);
                Log::warning("LONG_SQL ${time}ms, $query, $param");
            }
        });
    }
}
