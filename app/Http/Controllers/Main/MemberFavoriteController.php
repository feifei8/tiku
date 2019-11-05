<?php

namespace App\Http\Controllers\Main;


use App\Http\Controllers\Support\BaseController;
use App\Types\MemberFavoriteCategory;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Member\Services\MemberFavoriteService;
use Edwin404\Member\Support\MemberLoginCheck;
use Edwin404\Tecmz\Traits\MemberFavoriteTrait;

class MemberFavoriteController extends BaseController implements MemberLoginCheck
{
    use MemberFavoriteTrait;

    public function question(MemberFavoriteService $memberFavoriteService)
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getInteger('page');
        $pageSize = 10;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];

        $paginateData = $memberFavoriteService->paginate($this->memberUserId(), MemberFavoriteCategory::QUESTION, $page, $pageSize, $option);
        $pageHtml = PageHelper::render($paginateData['total'], $pageSize, $page, '?' . RequestHelper::mergeQueries(['page' => ['{page}']]));
        ModelHelper::modelJoin($paginateData['records'], 'categoryId', '_question', 'question', 'id');
        return $this->_view('member.favorite.question', [
            'records' => $paginateData['records'],
            'pageHtml' => $pageHtml,
        ]);
    }
}