<?php

namespace App\Http\Controllers\Admin;

class PartnerController extends \Edwin404\Partner\Controllers\Admin\PartnerController
{
    protected function setUpConfig()
    {
        $this->cmsConfigBasic['fields']['position']['options'] = [
            'pcHome' => 'PC首页',
        ];
    }


}