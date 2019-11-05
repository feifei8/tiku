<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Types\EventShowStatus;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Partner\Services\PartnerService;
use Edwin404\Tecmz\Traits\MemberAccountTrait;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class IndexController extends BaseController
{
    use MemberAccountTrait;

    public function index(BannerService $bannerService,
                          PartnerService $partnerService)
    {
        $banners = $bannerService->listByPositionWithCache('pcHome');
        $tags = $this->questionService->getTags();
        $partners = $partnerService->listByPositionWithCache('pcHome');

        $option = [];
        $option['order'] = ['id', 'desc'];
        $paginateData = $this->questionService->paginateQuestion(1, 10, $option);
        $latestQuestions = $paginateData['records'];

        return $this->_view('index', compact('banners', 'tags', 'partners', 'latestQuestions'));
    }

}