<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Services\PaperService;
use App\Services\QuestionService;
use App\Types\EventShowStatus;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\HtmlHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\TagHelper;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Tecmz\Service\AdService;
use Edwin404\Tecmz\Traits\MemberAccountTrait;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class PaperController extends BaseController
{
    public function index(PaperService $paperService,
                          AdService $adService)
    {
        $page = intval(Input::get('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 10;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];
        $option['where']['isPublic'] = true;
        $categoryId = Input::get('category_id');
        if ($categoryId) {
            $option['where']['categoryId'] = $categoryId;
        }

        $paginateData = ModelHelper::modelPaginate('paper', $page, $pageSize, $option);
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));

        $papers = $paginateData['records'];
        $paperCategories = $paperService->listCategories();

        ModelHelper::modelJoin($papers, 'categoryId', '_category', 'paper_category', 'id');

        return $this->_view('paper.index', [
            'pageHtml' => $pageHtml,
            'papers' => $papers,
            'paperCategories' => $paperCategories,
            'categoryId' => $categoryId,
            'ads' => $adService->listByPositionWithCache('pcPaperListRight')
        ]);
    }

    public function view(PaperService $paperService,
                         QuestionService $questionService,
                         $alias)
    {
        $paper = $paperService->getPaperByAlias($alias);
        if (empty($paper)) {
            return Response::send(-1, '试卷不存在');
        }

        if (!$paper['isPublic']) {
            return Response::send(-1, '试卷未公开');
        }

        $paperQuestions = $paperService->listQuestions($paper['id']);
        foreach ($paperQuestions as &$paperQuestion) {
            $paperQuestion['_questionData'] = $questionService->getQuestionData($paperQuestion['questionId']);
        }

        return $this->_view('paper.view', compact(
            'paper', 'paperQuestions'
        ));
    }

}