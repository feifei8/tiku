<?php

namespace Edwin404\Tecmz\Traits;


use Doctrine\Common\Cache\FilesystemCache;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Types\Gender;
use Edwin404\Member\Types\ProfileGender;
use Edwin404\Oauth\Core\Oauth;
use Edwin404\Oauth\Support\OauthTrait;
use Edwin404\Oauth\Types\OauthType;
use Edwin404\Tecmz\Helpers\MailHelper;
use Edwin404\Tecmz\Helpers\OauthHelper;
use Edwin404\Tecmz\Helpers\SmsHelper;
use Edwin404\Tecmz\Types\MailTemplate;
use Edwin404\Tecmz\Types\SmsTemplate;
use Edwin404\Wechat\Support\Application;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;

trait UserAccountTrait
{
    private function getOauthConfig($type)
    {
        $config = [
            'APP_KEY' => null,
            'APP_SECRET' => null,
            'CALLBACK' => Response::schema() . '://' . Request::server('HTTP_HOST') . '/user/oauth_callback_' . $type,
        ];
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
                if (!OauthHelper::isWechatMobileEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigFacade::get('oauthWechatMobileAppId');
                $config['APP_SECRET'] = ConfigFacade::get('oauthWechatMobileAppSecret');
                return $config;
//            case OauthType::QQ:
//                if (!OauthHelper::isQQEnable()) {
//                    return null;
//                }
//                $config['APP_KEY'] = ConfigFacade::get('oauthQQKey');
//                $config['APP_SECRET'] = ConfigFacade::get('oauthQQAppSecret');
//                return $config;
//            case OauthType::WEIBO:
//                if (!OauthHelper::isWeiboEnable()) {
//                    return null;
//                }
//                $config['APP_KEY'] = ConfigFacade::get('oauthWeiboKey');
//                $config['APP_SECRET'] = ConfigFacade::get('oauthWeiboAppSecret');
//                return $config;
//            case OauthType::WECHAT:
//                if (!OauthHelper::isWechatEnable()) {
//                    return null;
//                }
//                $config['APP_KEY'] = ConfigFacade::get('oauthWechatAppId');
//                $config['APP_SECRET'] = ConfigFacade::get('oauthWechatAppSecret');
//                return $config;
        }
        return null;
    }

    public function oauthLogin($type)
    {
        $config = $this->getOauthConfig($type);
        if (empty($config)) {
            return Response::send(-1, '授权登录配置错误');
        }

        if (empty($config['APP_KEY']) || empty($config['APP_SECRET'])) {
            return Response::send(-1, 'APP_KEY和APP_SECRET不能为空');
        }

        $oauthWechatProxy = ConfigFacade::get('oauthWechatMobileProxy');
        if ($oauthWechatProxy && in_array($type, [OauthType::WECHAT_MOBILE])) {
            $url = $oauthWechatProxy
                . '?appid=' . $config['APP_KEY'] . '&scope=snsapi_userinfo&redirect_uri='
                . urlencode($config['CALLBACK']);
        } else {
            $sns = Oauth::getInstance($type, $config);
            $url = $sns->getRequestCodeURL();
        }

        $redirect = Input::get('redirect', '/');
        if (!empty($redirect)) {
            Session::put('oauthRedirect', $redirect);
        }

        return redirect($url);
    }

    public function oauthCallback($type)
    {
        $code = Input::get('code', '');
        if (empty($code)) {
            return Response::send(-1, '登录失败(code为空)');
        }

        $config = $this->getOauthConfig($type);
        if (empty($config)) {
            return Response::send(-1, '授权登录配置错误');
        }

        $oauth = Oauth::getInstance($type, $config);

        $token = null;
        $openid = null;
        try {
            $token = $oauth->getAccessToken($code, null);
            $openid = $oauth->openid();
        } catch (\Exception $e) {
            return Response::send(-1, '登录失败(' . $e->getMessage() . ')');
        }

        if (empty($token) || empty($openid)) {
            return Response::send(-1, '登录失败(token=' . print_r($token, true) . ',openid=' . $openid . ')');
        }

        $userInfo = [];

        switch ($type) {
            case OauthType::WECHAT_MOBILE:
//            case OauthType::WECHAT:
                $data = $oauth->call('sns/userinfo');
                if (!empty($data ['errcode'])) {
                    return Response::send(-1, "微信登录失败：" . $data['errmsg']);
                }
                $userInfo['username'] = $data['nickname'];
                $userInfo['avatar'] = $data['headimgurl'];
                $userInfo['unionId'] = empty($data['unionid']) ? null : $data['unionid'];
                if ($data['sex'] == 2) {
                    $userInfo['gender'] = Gender::FEMALE;
                } else if ($data['sex'] == 1) {
                    $userInfo['gender'] = Gender::MALE;
                } else {
                    $userInfo['gender'] = Gender::UNKNOWN;
                }
                $userInfo['province'] = $data['province'];
                $userInfo['city'] = $data['city'];
                $userInfo['country'] = $data['country'];

                try {
                    $options = [
                        'debug' => true,
                        'app_id' => $config['APP_KEY'],
                        'secret' => $config['APP_SECRET'],
                        'token' => '',
                        'aes_key' => '',
                        'cache' => new FilesystemCache(config('wechat.cacheFilePath')),
                    ];
                    $app = new Application($options);
                    $wechatUserInfo = $app->user->get($openid);
                } catch (\Exception $e) {
                }
                $userInfo['subscribe'] = (empty($wechatUserInfo['subscribe']) ? 0 : 1);
                break;
//            case OauthType::QQ:
//                $data = $oauth->call('user/get_user_info');
//                if (!isset($data['ret']) || $data['ret'] != 0) {
//                    return Response::send(-1, 'QQ登录失败:' . json_encode($data));
//                }
//                $userInfo['username'] = $data['nickname'];
//                foreach (['figureurl_qq_2', 'figureurl_2', 'figureurl_qq_1', 'figureurl_1', 'figureurl'] as $avatarField) {
//                    if (isset($data[$avatarField]) && $data[$avatarField]) {
//                        $userInfo['avatar'] = $data[$avatarField];
//                    }
//                }
//                break;
//            case OauthType::WEIBO:
//                $data = $oauth->call('users/show', "uid=" . $openid);
//                if (!isset($data ['error_code']) || $data ['error_code'] != 0) {
//                    return Response::send(-1, 'QQ登录失败:' . json_encode($data));
//                }
//                $userInfo['username'] = $data['screen_name'];
//                $userInfo['avatar'] = empty($data['profile_image_url']) ? null : $data['profile_image_url'];
//                break;
        }
        if (empty($userInfo)) {
            return Response::send(-1, '获取用户信息失败');
        }

        Session::put('oauthOpenId', $openid);
        Session::put('oauthUserInfo', $userInfo);

//        if ($type == OauthType::WECHAT) {
//            return '<script>window.parent.location.href="/oauth_bind_' . $type . '";</script>';
//        }
        return Response::send(0, null, null, '/user/oauth_bind_' . $type);
    }

    public function oauthBind($type)
    {
        // TODO
//        $redirect = Session::get('oauthRedirect', '/');
//        $oauthOpenId = Session::get('oauthOpenId', null);
//        $oauthUserInfo = Session::get('oauthUserInfo', null);
//        if (empty($oauthOpenId) || empty($oauthUserInfo)) {
//            return Response::send(-1, '用户授权数据为空');
//        }
//
//        //如果用户已经登录直接关联到当前用户
//        if ($this->memberUserId()) {
//            switch ($type) {
//                case OauthType::WECHAT_MOBILE:
//                case OauthType::WECHAT:
//                    if (!empty($oauthUserInfo['unionId'])) {
//                        // 有开放平台关联微信手机登录和微信Web登录
//                        $memberUserId = $memberService->getMemberUserIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
//                        if ($memberUserId && $this->memberUserId() != $memberUserId) {
//                            $memberService->forgetOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
//                        }
//                        $memberService->putOauth($this->memberUserId(), OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
//                    }
//                    $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
//                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
//                        $memberService->forgetOauth($type, $oauthOpenId);
//                    }
//                    $memberService->putOauth($this->memberUserId(), $type, $oauthOpenId);
//                    break;
//                case OauthType::QQ:
//                case OauthType::WEIBO:
//                    $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
//                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
//                        $memberService->forgetOauth($type, $oauthOpenId);
//                    }
//                    $memberService->putOauth($this->memberUserId(), $type, $oauthOpenId);
//                    break;
//            }
//            Session::forget('oauthRedirect');
//            Session::forget('oauthOpenId');
//            Session::forget('oauthUserInfo');
//            return Response::send(0, null, null, $redirect);
//        }
//
//        // 查看用户是否已经登录
//        switch ($type) {
//            case OauthType::WECHAT_MOBILE:
//            case OauthType::WECHAT:
//                if (!empty($oauthUserInfo['unionId'])) {
//                    // 有开放平台关联微信手机登录和微信Web登录
//                    $memberUserId = $memberService->getMemberUserIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
//                    if ($memberUserId) {
//                        $memberService->putOauth($memberUserId, $type, $oauthOpenId);
//                        Session::put('memberUserId', $memberUserId);
//                        Session::forget('oauthRedirect');
//                        Session::forget('oauthOpenId');
//                        Session::forget('oauthUserInfo');
//                        return Response::send(0, null, null, $redirect);
//                    }
//                }
//                $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
//                if ($memberUserId) {
//                    Session::put('memberUserId', $memberUserId);
//                    Session::forget('oauthRedirect');
//                    Session::forget('oauthOpenId');
//                    Session::forget('oauthUserInfo');
//                    return Response::send(0, null, null, $redirect);
//                }
//                break;
//            case OauthType::QQ:
//            case OauthType::WEIBO:
//                $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
//                if ($memberUserId) {
//                    Session::put('memberUserId', $memberUserId);
//                    Session::forget('oauthRedirect');
//                    Session::forget('oauthOpenId');
//                    Session::forget('oauthUserInfo');
//                    return Response::send(0, null, null, $redirect);
//                }
//                break;
//        }
//
//        if (Request::isMethod('post')) {
//            $username = Input::get('username');
//            $ret = $memberService->register($username, null, null, null, true);
//            if ($ret['code']) {
//                return Response::send(-1, $ret['msg']);
//            }
//            $memberUserId = $ret['data']['id'];
//            switch ($type) {
//                case OauthType::WECHAT_MOBILE:
//                    if (!empty($oauthUserInfo['unionId'])) {
//                        $memberService->putOauth($memberUserId, OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
//                    }
//                    $memberService->putOauth($memberUserId, $type, $oauthOpenId);
//                    break;
//                case OauthType::QQ:
//                case OauthType::WEIBO:
//                    $memberService->putOauth($memberUserId, $type, $oauthOpenId);
//                    break;
//                default:
//                    return Response::send(-1, 'oauthType error');
//            }
//            Session::put('memberUserId', $memberUserId);
//            Session::forget('oauthRedirect');
//            Session::forget('oauthOpenId');
//            Session::forget('oauthUserInfo');
//            return Response::send(0, null, null, $redirect);
//        }
//
//        return $this->_view('oauthBind', compact('oauthOpenId', 'oauthUserInfo', 'redirect'));
    }

}