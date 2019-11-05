<?php

namespace App\Http\Controllers\Admin;


use Edwin404\Admin\Cms\Field\FieldSwitch;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Handle\ConfigCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Tecmz\Api\TecmzApiRequest;
use Illuminate\Support\Facades\Input;

class MarketingController extends AdminCheckController
{
    public function news(ConfigCms $configCms)
    {
        if (RequestHelper::isPost()) {

            if (Input::get('marketingNewsEnable')) {
                $appId = Input::get('marketingNewsTecmzApiAppId');
                $ret = TecmzApiRequest::instance($appId)->ping();
                if ($ret['code']) {
                    return Response::send(-1, 'AppId不正确');
                }
            }

        }

        return $configCms->execute($this, [
            'group' => 'news',
            'pageTitle' => '自动发布资讯',
            'fields' => [
                'marketingNewsEnable' => ['type' => FieldSwitch::class, 'title' => '开启资讯自动发布', 'desc' => '
使用方法:
<br />
1. 请至 <a href="http://api.tecmz.com" target="_blank">http://api.tecmz.com</a> 注册账号并申请 新闻资讯 接口；
<br />
2. 在墨子API填写本网站网址 <code>' . RequestHelper::domainUrl() . '</code>；
<br />
3. 在本站设置AppId，发布数量，标签。
'],
                'marketingNewsTecmzApiAppId' => ['type' => FieldText::class, 'title' => '墨子API接口AppId', 'desc' => ''],
                'marketingNewsDailyPostCount' => ['type' => FieldText::class, 'title' => '每日自动发布数量', 'desc' => '数值范围 1-10'],
                'marketingNewsTag' => ['type' => FieldText::class, 'title' => '资讯标签', 'desc' => '多个标签使用逗号“,”分割，如果留空表示随机新闻'],
            ]
        ]);
    }

}