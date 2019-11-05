<?php

namespace App\Http\Controllers\Main;


use App\Helpers\MailHelper;
use App\Helpers\SmsHelper;
use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Data\Services\DataService;
use Edwin404\Forum\Events\ThreadHasNewPost;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Member\Services\MemberMessageService;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Support\MemberLoginCheck;
use Edwin404\Member\Types\MemberMessageStatus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MemberMessageController extends BaseController implements MemberLoginCheck
{
    public function index(MemberMessageService $memberMessageService)
    {
        $page = 1;
        $pageSize = 999;
        $option = [];
        $option['order'] = ['id', 'desc'];
        $option['where'] = ['status' => MemberMessageStatus::UNREAD];

        $paginateData = $memberMessageService->paginate($this->memberUserId(), $page, $pageSize, $option);
        $messages = $paginateData['records'];

        return $this->_view('member.message', compact('messages'));
    }

    public function markRead(MemberMessageService $memberMessageService)
    {
        $ids = Input::get('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return Response::send(0, null);
        }
        $memberMessageService->setMemberMessageRead($this->memberUserId(), $ids);
        return Response::send(0, null);
    }

    public function markReadAll(MemberMessageService $memberMessageService)
    {
        $memberMessageService->setMemberMessageReadAll($this->memberUserId());
        return Response::send(0, null);
    }

}