<?php

namespace App\Http\Controllers\Admin;


use Edwin404\Admin\Cms\Handle\ConfigCms;

class ConfigController extends \Edwin404\Tecmz\Controllers\ConfigController
{
    public function setting(ConfigCms $configCms, $param = [])
    {
        return parent::setting($configCms, [
            'siteTemplateOptions' => [
                'default' => '默认模板',
                'dark' => '炫酷黑色',
            ]
        ]);
    }
}