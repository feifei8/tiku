<?php

namespace Edwin404\Tecmz\Traits;


use Edwin404\Config\Facades\ConfigFacade;

trait AssetsCDNTrait
{
    public function bootAssetsCDN()
    {
        // 初次安装时候无数据库信息下面代码会报错
        try {

            // 设置CDN
            if (env('URL_CDN')) {
                $this->app->config->set('smart-assets.assets_cdn', env('URL_CDN'));
            } else {
                $cdn = ConfigFacade::get('systemCdnUrl', '/');
                if (empty($cdn)) {
                    $cdn = env('URL_CDN', '/');
                }
                $this->app->config->set('smart-assets.assets_cdn', $cdn);
            }

        } catch (\Exception $e) {
            //Do Nothing
        }
    }
}