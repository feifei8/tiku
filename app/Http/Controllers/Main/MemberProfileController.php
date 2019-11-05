<?php

namespace App\Http\Controllers\Main;


use App\Helpers\MailHelper;
use App\Helpers\SmsHelper;
use App\Http\Controllers\Support\BaseController;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\PageHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Data\Services\DataService;
use Edwin404\Forum\Services\ForumService;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Support\MemberLoginCheck;
use Edwin404\Oauth\Types\OauthType;
use Edwin404\Tecmz\Traits\MemberProfileTrait;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MemberProfileController extends BaseController implements MemberLoginCheck
{
    use MemberProfileTrait;

    public function index(MemberService $memberService)
    {
        if ($this->isMobile()) {
            $isWechatAuth = false;
            if (ConfigFacade::get('oauthWechatEnable', false)) {
                $isWechatAuth = $memberService->getOauthOpenId($this->memberUserId(), OauthType::WECHAT);
            }
            $isQQAuth = false;
            if (ConfigFacade::get('oauthQQEnable', false)) {
                $isQQAuth = $memberService->getOauthOpenId($this->memberUserId(), OauthType::QQ);
            }
            $isWeiboAuth = false;
            if (ConfigFacade::get('oauthWeiboEnable', false)) {
                $isWeiboAuth = $memberService->getOauthOpenId($this->memberUserId(), OauthType::WEIBO);
            }
            return $this->_view('member.profile.index', [
                'isQQAuth' => $isQQAuth,
                'isWeiboAuth' => $isWeiboAuth,
                'isWechatAuth' => $isWechatAuth,
            ]);
        }
        return Response::send(0, null, null, '/member/profile_basic');
    }

    public function basic(MemberService $memberService)
    {
        if (Request::isMethod('post')) {

            $data = [];
            $data['gender'] = intval(Input::get('gender'));
            $data['realname'] = trim(Input::get('realname'));
            $data['signature'] = trim(Input::get('signature'));

            $memberUser = $this->memberUser();
            if (empty($memberUser['username'])) {
                $username = trim(Input::get('username'));
                if ($username) {
                    $existsMemberUser = $memberService->loadByUsername($username);
                    if (!empty($existsMemberUser)) {
                        return Response::send(-1, '用户名已被占用');
                    }
                    $data['username'] = $username;
                }
            }

            $memberService->update($this->memberUserId(), $data);

            return Response::send(0, '保存成功');

        }
        return $this->_view('member.profile.basic');
    }

}