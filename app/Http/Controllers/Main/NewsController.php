<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Tecmz\Service\AdService;
use Illuminate\Support\Facades\Input;

class NewsController extends BaseController
{

    public function index(AdService $adService)
    {
        $page = Input::get('page', 1);
        $pageSize = 10;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];

        $categoryId = Input::get('category_id');
        if ($categoryId) {
            $option['where']['categoryId'] = $categoryId;
        }

        $paginateData = ModelHelper::modelPaginate('news', $page, $pageSize, $option);
        $categories = ModelHelper::model('news_category')->orderBy('sort', 'asc')->get()->toArray();

        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));
        $news = $paginateData['records'];

        foreach ($news as &$new) {
            $info = HtmlHelper::extractTextAndImages($new['content']);
            $new['summary'] = $info['text'];
        }

        $dataView = [];
        $dataView['news'] = $news;
        $dataView['categoryId'] = $categoryId;
        $dataView['categories'] = $categories;
        $dataView['pageHtml'] = $pageHtml;
        $dataView['ads'] = $adService->listByPositionWithCache('pcNewsListRight');
        return $this->_view('news.list', $dataView);
    }

    public function view(AdService $adService, $id)
    {
        $news = ModelHelper::load('news', ['id' => $id]);
        if (empty($news)) {
            return Response::send(-1, 'new not found');
        }

        $news['_category'] = ModelHelper::load('news_category', ['id' => $news['categoryId']]);

        $newsLatest = ModelHelper::model('news')->orderBy('id', 'desc')->limit(6)->get()->toArray();

        $dataView = [];
        $dataView['news'] = $news;
        $dataView['newsLatest'] = $newsLatest;
        $dataView['ads'] = $adService->listByPositionWithCache('pcNewsViewRight');
        return $this->_view('news.view', $dataView);
    }

}