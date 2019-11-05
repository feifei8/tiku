<?php

namespace App\Http\Controllers\Admin;

class AdController extends \Edwin404\Tecmz\Controllers\AdController
{
    protected function setUpConfig()
    {
        $this->cmsConfigBasic['fields']['position']['options'] = [
            'pcQuestionViewRight' => '题目查看右侧',
            'pcNewsListRight' => '资讯列表右侧',
            'pcNewsViewRight' => '资讯查看右侧',
            'pcPaperListRight' => '试卷列表右侧',
        ];
    }


}