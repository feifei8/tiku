<?php

namespace App\Http\Controllers\Admin;

class BannerController extends \Edwin404\Tecmz\Controllers\BannerController
{
    protected function setUpConfig()
    {
        $this->cmsConfigBasic['fields']['position']['options'] = [
            'pcHome' => 'PC首页',
            'mHome' => '手机首页',
        ];
    }


}