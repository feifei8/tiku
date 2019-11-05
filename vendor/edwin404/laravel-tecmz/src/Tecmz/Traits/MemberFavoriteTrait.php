<?php

namespace Edwin404\Tecmz\Traits;


use App\Types\MemberFavoriteCategory;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\Response;
use Edwin404\Base\Support\TypeHelper;
use Edwin404\Member\Services\MemberFavoriteService;

trait MemberFavoriteTrait
{
    public function submit(MemberFavoriteService $memberFavoriteService)
    {
        $input = InputPackage::buildFromInput();
        $action = $input->getTrimString('action');
        $category = $input->getTrimString('category');
        $categoryId = $input->getInteger('categoryId');
        $name = TypeHelper::name(MemberFavoriteCategory::class, $category);
        if (empty($name)) {
            return Response::send(-1, '数据错误');
        }
        switch ($action) {
            case 'favorite':
                $memberFavoriteService->add($this->memberUserId(), $category, $categoryId);
                break;
            case 'unfavorite':
                $memberFavoriteService->delete($this->memberUserId(), $category, $categoryId);
                break;
        }
        return Response::send(0, 'ok');
    }


}