<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use App\Types\EventShowStatus;
use Edwin404\Banner\Services\BannerService;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Tecmz\Traits\MemberAccountTrait;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class TagsController extends BaseController
{
    public function index()
    {
        $tags = $this->questionService->getTags();
        return $this->_view('tags.index', compact('tags'));
    }

}