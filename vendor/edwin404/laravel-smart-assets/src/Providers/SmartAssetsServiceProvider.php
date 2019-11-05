<?php

namespace Edwin404\SmartAssets\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SmartAssetsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/smart-assets.php' => config_path('smart-assets.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/smart-assets.php', 'smart-assets'
        );

        $this->app->singleton('smartAssetsPath', config('smart-assets.assets_path_service'));

        Blade::directive('assets', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $file = trim($mat[1], '\'" "');
                return app('smartAssetsPath')->getCDN($file) . app('smartAssetsPath')->getPathWithHash($file);
            } else {
                return '';
            }
        });

        Blade::directive('assetsData', function ($expression = '') {
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                return "<" . "?php echo empty($mat[1])?" . json_encode(config('smart-assets.assets_image_none', '')) . ":\\Edwin404\\SmartAssets\\Helper\\AssetsHelper::fix($mat[1]); ?" . ">";
            }
            return "";
        });

    }
}
