<?php

namespace App\Http\Controllers\Main;


use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Forum\Helpers\PostHelper;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Support\MemberLoginCheck;
use Illuminate\Support\Facades\Input;

class MemberController extends BaseController
{
    public function index()
    {
        // 手机没有登录的情况下可以查看个人中心
        if ($this->isMobile()) {
            return $this->_view('member.index');
        } else {
            return Response::send(0, null, null, '/member/profile');
        }
    }

}