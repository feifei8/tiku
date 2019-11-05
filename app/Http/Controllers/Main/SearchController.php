<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Types\EventShowStatus;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Partner\Services\PartnerService;
use Edwin404\Tecmz\Traits\MemberAccountTrait;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class SearchController extends BaseController
{

    public function index()
    {
        $input = InputPackage::buildFromInput();
        $keywords = $input->getTrimString('keywords');
        return Response::send(0, null, null, '/search/question?keywords=' . urlencode($keywords));
    }

    public function question()
    {
        $input = InputPackage::buildFromInput();
        $keywords = $input->getTrimString('keywords');
        $page = $input->getInteger('page');
        $pageSize = 10;

        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];
        $option['whereOperate'] = [];
        $option['whereOperate'][] = ['question', 'like', '%' . $keywords . '%'];
        $paginateData = $this->questionService->paginateQuestion($page, $pageSize, $option);
        $records = $paginateData['records'];
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));

        $viewData = [];
        $viewData['pageTitle'] = '搜索题目"' . htmlspecialchars($keywords) . '"';
        $viewData['pageDescription'] = '搜索题目"' . htmlspecialchars($keywords) . '"';
        $viewData['pageKeywords'] = '搜索题目"' . htmlspecialchars($keywords) . '"';
        $viewData['keywords'] = $keywords;
        $viewData['records'] = $records;
        $viewData['pageHtml'] = $pageHtml;
        return $this->_view('search.question', $viewData);
    }

    public function paper()
    {
        $input = InputPackage::buildFromInput();
        $keywords = $input->getTrimString('keywords');
        $page = $input->getInteger('page');
        $pageSize = 10;

        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = [];
        $option['whereOperate'] = [];
        $option['whereOperate'][] = ['title', 'like', '%' . $keywords . '%'];
        $paginateData = ModelHelper::modelPaginate('paper', $page, $pageSize, $option);
        $records = $paginateData['records'];
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));

        $viewData = [];
        $viewData['pageTitle'] = '搜索试卷"' . htmlspecialchars($keywords) . '"';
        $viewData['pageDescription'] = '搜索试卷"' . htmlspecialchars($keywords) . '"';
        $viewData['pageKeywords'] = '搜索试卷"' . htmlspecialchars($keywords) . '"';
        $viewData['keywords'] = $keywords;
        $viewData['records'] = $records;
        $viewData['pageHtml'] = $pageHtml;
        return $this->_view('search.paper', $viewData);
    }

}