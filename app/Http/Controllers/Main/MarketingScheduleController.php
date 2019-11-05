<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Types\EventShowStatus;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Data\Services\DataService;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Partner\Services\PartnerService;
use Edwin404\Tecmz\Api\TecmzApiRequest;
use Edwin404\Tecmz\Traits\MemberAccountTrait;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class MarketingScheduleController extends BaseController
{
    public function index(DataService $dataService)
    {

        if (ConfigFacade::get('marketingNewsEnable', false)) {

            $appId = trim(ConfigFacade::get('marketingNewsTecmzApiAppId'));
            $postCount = intval(ConfigFacade::get('marketingNewsDailyPostCount'));
            if ($postCount < 0) {
                $postCount = 0;
            } else if ($postCount > 10) {
                $postCount = 10;
            }
            if (intval(date('H')) > 8) {

                $todayCount = ModelHelper::model('news')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', time()))
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', time()))
                    ->count();
                if ($appId && $postCount > 0 && $todayCount < $postCount) {

                    $tag = trim(ConfigFacade::get('marketingNewsTag'));
                    $ret = TecmzApiRequest::instance($appId)->news(1, $tag);
                    if ($ret['code'] == 0) {
                        $list = $ret['data']['list'];
                        foreach ($list as $news) {
                            if (ModelHelper::exists('news', [
                                'title' => $news['title']
                            ])
                            ) {
                                continue;
                            }
                            $saveNews = [];
                            $saveNews['categoryId'] = 0;
                            $saveNews['title'] = $news['title'];
                            $saveNews['content'] = $news['content'];
                            $saveNews['content'] = $dataService->storeContentRemoteImages($saveNews['content']);
                            ModelHelper::add('news', $saveNews);
                        }
                    }
                }

            }
        }

        return 'success';
    }

}