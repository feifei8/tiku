<?php

namespace App\Http\Controllers\Main;


use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\Response;

class ArticleController extends BaseController
{
    public function index($id)
    {
        $article = ModelHelper::load('article', ['id' => $id]);
        if (empty($article)) {
            return Response::send(0, null, null, '/');
        }
        return $this->_view('article.index', compact('article'));
    }
}