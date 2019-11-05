<?php

namespace Edwin404\Tecmz\Traits;


use Edwin404\Api\Services\ApiSessionService;
use Edwin404\Base\Support\CurlHelper;
use Edwin404\Base\Support\FileHelper;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\InputTypeHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Member\Events\MemberUserRegisteredEvent;
use Edwin404\Member\Services\MemberService;
use Edwin404\Member\Types\ProfileGender;
use Edwin404\Oauth\Core\Oauth;
use Edwin404\Oauth\Support\OauthTrait;
use Edwin404\Oauth\Types\OauthType;
use Edwin404\Tecmz\Helpers\ConfigEnvHelper;
use Edwin404\Tecmz\Helpers\MailHelper;
use Edwin404\Tecmz\Helpers\OauthHelper;
use Edwin404\Tecmz\Helpers\SmsHelper;
use Edwin404\Tecmz\Types\MailTemplate;
use Edwin404\Tecmz\Types\MemberRegisterType;
use Edwin404\Tecmz\Types\SmsTemplate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;

//Route::match(['get', 'post'],'login', '\App\Http\Controllers\Main\IndexController@login');
//Route::match(['get', 'post'],'login/captcha', '\App\Http\Controllers\Main\IndexController@loginCaptcha');
//Route::match(['get', 'post'],'logout', '\App\Http\Controllers\Main\IndexController@logout');
//Route::match(['get', 'post'], 'register', '\App\Http\Controllers\Main\IndexController@register');
//Route::match(['get', 'post'], 'register/username', '\App\Http\Controllers\Main\IndexController@registerUsername');
//Route::match(['get', 'post'], 'register/phone', '\App\Http\Controllers\Main\IndexController@registerPhone');
//Route::match(['get', 'post'], 'register/phone_verify', '\App\Http\Controllers\Main\IndexController@registerPhoneVerify');
//Route::match(['get', 'post'], 'register/email', '\App\Http\Controllers\Main\IndexController@registerEmail');
//Route::match(['get', 'post'], 'register/email_verify', '\App\Http\Controllers\Main\IndexController@registerEmailVerify');
//Route::match(['get', 'post'], 'register/captcha', '\App\Http\Controllers\Main\IndexController@registerCaptcha');
//Route::match(['get', 'post'], 'register/bind', '\App\Http\Controllers\Main\IndexController@registerBind');
//Route::match(['get', 'post'],'retrieve', '\App\Http\Controllers\Main\IndexController@retrieve');
//Route::match(['get', 'post'],'retrieve/email', '\App\Http\Controllers\Main\IndexController@retrieveEmail');
//Route::match(['get', 'post'],'retrieve/email_verify', '\App\Http\Controllers\Main\IndexController@retrieveEmailVerify');
//Route::match(['get', 'post'],'retrieve/phone', '\App\Http\Controllers\Main\IndexController@retrievePhone');
//Route::match(['get', 'post'],'retrieve/phone_verify', '\App\Http\Controllers\Main\IndexController@retrievePhoneVerify');
//Route::match(['get', 'post'],'retrieve/captcha', '\App\Http\Controllers\Main\IndexController@retrieveCaptcha');
//Route::match(['get', 'post'],'retrieve/reset', '\App\Http\Controllers\Main\IndexController@retrieveReset');
//Route::get('sso/client', '\App\Http\Controllers\Main\IndexController@ssoClient');
//Route::get('sso/server', '\App\Http\Controllers\Main\IndexController@ssoServer');
//Route::get('sso/server_success', '\App\Http\Controllers\Main\IndexController@ssoServerSuccess');
//Route::get('sso/server_logout', '\App\Http\Controllers\Main\IndexController@ssoServerLogout');

//Route::match(['get', 'post'],'token_login', '\App\Http\Controllers\Main\IndexController@tokenLogin');
//Route::match(['get', 'post'], 'token_bind_oauth/{callback}', '\App\Http\Controllers\Main\IndexController@tokenBindOauth');

/**
 * 墨子系列产品 SSO Server 登录服务流程
 *
 * 一个 Server 可以提供 N 个 Client 登录
 * 1. 检测到 Client 跳转过来的登录请求,附带以下参数;
 *                 client     -> http://client.com/sso/client
 *                 timestamp  -> time()
 *                 sign       -> md5( md5(ssoSecret) + md5(timestamp) + md5(client) )
 * 2. 检测 SSO 登录是否开启 (config.ssoServerEnable);
 *    验证 sign 是否正确;
 *    验证 timestamp 是否合法,误差不能超过 1 天;
 *    验证 client 是否为预期 (config.ssoClientList);
 * 3. Server 使用 Session 记录下 client 为 Session.ssoClient;
 * 4. 如果用户已经登录, 直接跳转到 /sso/server_success;
 * 5. 跳转到登录页面 /login 并附带以下参数:
 *                 redirect     -> /sso/server_success
 * 6. 用户使用不同方式自行登录;
 * 7. 跳转到 Session.ssoClient 并附带以下参数
 *                 server     -> http://server.com/sso/server
 *                 timestamp  -> time()
 *                 username   -> base64_encode(username)
 *                 sign       -> md5( md5(ssoSecret) + md5(timestamp) + md5(server) + md5(username) )
 */


/**
 * 墨子系列产品 SSO Client 登录流程
 *
 * 一个 Server 可以提供 N 个 Client 登录
 * 1. Client 需要用户登录,跳转到 /login?redirect=<login-to-go> 地址;
 * 2. Client 检测到开启了 SSO 登录 (config.ssoClientEnable) ,重定向到 /sso/client?redirect=<login-to-go>;
 * 3. Client 使用 Session 记录下 redirect 为 Session.ssoRedirect, 跳转到 (ssoServer) http://server.com/sso/server 带以下参数:
 *                 client     -> http://client.com/sso/client
 *                 timestamp  -> time()
 *                 sign       -> md5( md5(ssoSecret) + md5(timestamp) + md5(client) )
 * 4. Server 端授权登录跳回 http://client.com/sso/client_success 并附带参数
 *                 server     -> http://server.com/sso/server
 *                 timestamp  -> time()
 *                 username   -> base64_encode(username)
 *                 sign       -> md5( md5(ssoSecret) + md5(timestamp) + md5(server) + md5(username) )
 * 5. 验证 sign 是否正确;
 *    验证 timestamp 是否合法,误差不能相差 1 天;
 *    验证 server 是否为预期 (config.ssoServer)
 * 6. 根据 username 来进行登录,如果用户不存在创建用户,如果用户已经存在直接设置为已登录状态;
 * 7. 跳转到 Session.ssoRedirect .
 */


/**
 * 墨子系列产品 SSO Client 退出流程
 *
 * 一个 Server 可以提供 N 个 Client 登录
 * 1. Client 需要退出登陆,跳转到 /logout?redirect=<logout-to-go> 地址;
 * 2. Client 检测到开启了 SSO 登录 (config.ssoClientEnable) ,重定向到
 * http://server.com/sso/server_logout?redirect=urlencode(http://client.com/logout?server=true&redirect=<logout-to-go>);
 */
trait MemberAccountTrait
{

    public function ssoClient(MemberService $memberService)
    {
        if (!ConfigFacade::get('ssoClientEnable', false)) {
            return Response::send(-1, '请开启 同步登录客户端');
        }

        $ssoServer = ConfigFacade::get('ssoServer', '');
        if (empty($ssoServer)) {
            return Response::send(-1, '请配置 同步登录服务端地址');
        }

        $ssoSecret = ConfigFacade::get('ssoClientSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录客户端通讯秘钥');
        }

        $server = Input::get('server');
        if ($server) {

            $username = @base64_decode(Input::get('username', ''));
            $timestamp = Input::get('timestamp');
            $sign = Input::get('sign');

            if (empty($username)) {
                return Response::send(-1, '同步登录返回的用户名为空');
            }
            if (empty($timestamp)) {
                return Response::send(-1, 'timestamp empty');
            }
            if (empty($sign)) {
                return Response::send(-1, 'sign empty');
            }

            $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));
            if ($sign != $signCalc) {
                return Response::send(-1, 'sign error');
            }

            if (abs(time() - $timestamp) > 2400 * 2600) {
                return Response::send(-1, 'timestamp error');
            }

            if ($server != $ssoServer) {
                return Response::send(-1, '同步登录 服务端地址不是配置的' . $ssoServer);
            }

            $memberUser = $memberService->loadByUsername($username);
            if (empty($memberUser)) {
                $ret = $memberService->register($username, null, null, null, true);
                if ($ret['code']) {
                    return Response::send(-1, $ret['msg']);
                }
                $memberUser = $memberService->load($ret['data']['id']);
            }

            Session::put('memberUserId', $memberUser['id']);

            $ssoRedirect = Session::get('ssoRedirect', null);
            if (empty($ssoRedirect)) {
                return Response::send(0, '已经登录成功 但是没有找到跳转地址');
            }
            return Response::send(0, null, null, $ssoRedirect);

        } else {

            $redirect = trim(Input::get('redirect'));
            Session::put('ssoRedirect', $redirect);

            $client = RequestHelper::domainUrl() . '/sso/client';
            $timestamp = time();
            $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));

            $redirect = $ssoServer . '?client=' . urlencode($client) . '&timestamp=' . $timestamp . '&sign=' . $sign;

            return Response::send(0, null, null, $redirect);

        }

    }

    public function ssoServer()
    {
        $client = trim(Input::get('client'));
        $timestamp = intval(Input::get('timestamp'));
        $sign = trim(Input::get('sign'));
        if (empty($client)) {
            return Response::send(-1, 'client empty');
        }
        if (empty($timestamp)) {
            return Response::send(-1, 'timestamp empty');
        }
        if (empty($sign)) {
            return Response::send(-1, 'sign empty');
        }
        if (!ConfigFacade::get('ssoServerEnable', false)) {
            return Response::send(-1, '请开启 同步登录服务端');
        }
        $ssoSecret = ConfigFacade::get('ssoServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录服务端通讯秘钥');
        }
        $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
        if ($sign != $signCalc) {
            return Response::send(-1, 'sign error');
        }
        if (abs(time() - $timestamp) > 2400 * 2600) {
            return Response::send(-1, 'timestamp error');
        }
        $ssoClientList = explode("\n", ConfigFacade::get('ssoClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::send(-1, '请在 同步登陆服务端增加客户端地址 ' . $client);
        }
        Session::put('ssoClient', $client);

        if ($this->memberUserId()) {
            return Response::send(0, null, null, '/sso/server_success');
        }

        return Response::send(0, null, null, '/login?redirect=' . urlencode('/sso/server_success'));
    }

    public function ssoServerSuccess()
    {
        if (!$this->memberUserId()) {
            return Response::send(0, null, null, '/login?redirect=' . urlencode('/sso/server_success'));
        }
        $memberUser = $this->memberUser();

        $ssoSecret = ConfigFacade::get('ssoServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录服务端通讯秘钥');
        }

        $server = RequestHelper::domainUrl() . '/sso/server';
        $timestamp = time();
        $username = $memberUser['username'];
        $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $ssoClient = Session::get('ssoClient', '');
        if (empty($ssoClient)) {
            return Response::send(0, '登录成功但是没有找到客户端');
        }
        Session::forget('ssoClient', $ssoClient);

        $redirect = $ssoClient . '?server=' . urlencode($server) . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username)) . '&sign=' . $sign;

        return Response::send(0, null, null, $redirect);
    }

    public function ssoServerLogout()
    {
        Session::forget('memberUserId');
        $redirect = Input::get('redirect', '/');
        return Response::send(0, null, null, $redirect);
    }

    public function registerCaptcha()
    {
        return Captcha::create('default');
    }

    public function registerEmailVerify(MemberService $memberService)
    {

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '禁止注册');
        }

        $email = Input::get('target');
        if (empty($email)) {
            return Response::send(-1, '邮箱不能为空');
        }
        if (!InputTypeHelper::isEmail($email)) {
            return Response::send(-1, '邮箱格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUser = $memberService->loadByEmail($email);
        if (!empty($memberUser)) {
            return Response::send(-1, '邮箱已经被占用');
        }

        if (Session::get('registerEmailVerifyTime') && $email == Session::get('registerEmail')) {
            if (Session::get('registerEmailVerifyTime') + 60 * 10 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('registerEmailVerify', $verify);
        Session::put('registerEmailVerifyTime', time());
        Session::put('registerEmail', $email);

        MailHelper::send($email, '注册账户验证码', MailTemplate::VERIFY, ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');
    }

    public function registerEmail(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '网站禁止注册');
        }

        if (MemberRegisterType::EMAIL != ConfigFacade::get('registerType')) {
            return Response::send(-1, '网站不允许邮箱注册');
        }

        if (Request::isMethod('post')) {
            $email = trim(Input::get('email'));
            $emailVerify = Input::get('emailVerify', null);
            $password = Input::get('password', null);
            if (empty($password)) {
                return Response::send(-1, '密码不能为空');
            }
            if ($emailVerify != Session::get('registerEmailVerify')) {
                return Response::send(-1, '邮箱验证码不正确.');
            }
            if (Session::get('registerEmailVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '邮箱验证码已过期');
            }
            if ($email != Session::get('registerEmail')) {
                return Response::send(-1, '两次邮箱不一致');
            }
            $ret = $memberService->register(null, null, $email, $password);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));
            $memberService->update($memberUserId, ['phoneVerified' => true]);
            return Response::send(0, '注册成功，请您登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('registerEmail', compact('redirect'));
    }

    public function registerPhoneVerify(MemberService $memberService)
    {
        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '用户注册已禁用');
        }

        $phone = Input::get('target');
        if (empty($phone)) {
            return Response::send(-1, '手机不能为空');
        }
        if (!InputTypeHelper::isPhone($phone)) {
            return Response::send(-1, '手机格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUser = $memberService->loadByPhone($phone);
        if (!empty($memberUser)) {
            return Response::send(-1, '手机已经注册');
        }

        if (Session::get('registerPhoneVerifyTime') && $phone == Session::get('registerPhone')) {
            if (Session::get('registerPhoneVerifyTime') + 60 * 2 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('registerPhoneVerify', $verify);
        Session::put('registerPhoneVerifyTime', time());
        Session::put('registerPhone', $phone);

        SmsHelper::send($phone, SmsTemplate::VERIFY, ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');
    }

    public function registerPhone(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '网站禁止注册');
        }

        if (MemberRegisterType::PHONE != ConfigFacade::get('registerType')) {
            return Response::send(-1, '网站不允许手机注册');
        }

        if (Request::isMethod('post')) {
            $phone = trim(Input::get('phone'));
            $phoneVerify = Input::get('phoneVerify', null);
            $password = Input::get('password', null);
            if (empty($password)) {
                return Response::send(-1, '密码不能为空');
            }
            if ($phoneVerify != Session::get('registerPhoneVerify')) {
                return Response::send(-1, '手机验证码不正确.');
            }
            if (Session::get('registerPhoneVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '手机验证码已过期');
            }
            if ($phone != Session::get('registerPhone')) {
                return Response::send(-1, '两次手机不一致');
            }
            $ret = $memberService->register(null, $phone, null, $password);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));
            $memberService->update($memberUserId, ['phoneVerified' => true]);
            Session::put('memberUserId', $memberUserId);
            return Response::send(0, '注册成功，即将登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('registerPhone', compact('redirect'));
    }

    public function registerUsername(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '网站禁止注册');
        }

        if (MemberRegisterType::USERNAME != ConfigFacade::get('registerType', MemberRegisterType::USERNAME)) {
            return Response::send(-1, '网站不允许用户名注册');
        }

        if (Request::isMethod('post')) {
            $username = Input::get('username');
            $password = Input::get('password');
            $passwordRepeat = Input::get('passwordRepeat');
            if (empty($username)) {
                return Response::send(-1, '请输入用户名');
            }
            // 为了兼容使用使用统一登录
            if (Str::contains($username, '@')) {
                return Response::send(-1, '用户名不能包含特殊字符');
            }
            if (preg_match('/^\\d{11}$/', $username)) {
                return Response::send(-1, '用户名不能为纯数字');
            }
            if (empty($password)) {
                return Response::send(-1, '请输入密码');
            }
            if ($password != $passwordRepeat) {
                return Response::send(-1, '两次输入密码不一致');
            }

            $ret = $memberService->register($username, null, null, $password);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));
            return Response::send(0, '注册成功，请您登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('registerUsername', compact('redirect'));
    }

    public function registerBind(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if (!$this->memberUserId()) {
            return Response::send(0, null, null, '/');
        }

        if (Request::isMethod('post')) {
            $username = Input::get('username');
            if (empty($username)) {
                return Response::send(-1, '请输入用户名');
            }
            // 为了兼容使用使用统一登录
            if (Str::contains($username, '@')) {
                return Response::send(-1, '用户名不能包含特殊字符');
            }
            if (preg_match('/^\\d{11}$/', $username)) {
                return Response::send(-1, '用户名不能为纯数字');
            }

            $ret = $memberService->uniqueCheck('username', $username);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberService->update($this->memberUserId(), [
                'username' => $username
            ]);
            return Response::send(0, '绑定成功', null, $redirect);
        }

        return $this->_view('registerBind', compact('redirect'));
    }

    public function register()
    {
        $redirect = Input::get('redirect');
        switch (ConfigFacade::get('registerType')) {
            case MemberRegisterType::PHONE:
                return Response::send(0, null, null, '/register/phone?redirect=' . urlencode($redirect));
            case MemberRegisterType::EMAIL:
                return Response::send(0, null, null, '/register/email?redirect=' . urlencode($redirect));
            default:
                return Response::send(0, null, null, '/register/username?redirect=' . urlencode($redirect));
        }
    }

    public function login(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if (empty($redirect)) {
            $redirect = '/';
        }
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('ssoClientEnable', false)) {
            return Response::send(0, null, null, '/sso/client?redirect=' . urlencode($redirect));
        }

        if (Request::isMethod('post')) {
            $username = Input::get('username');
            $password = Input::get('password');
            if (empty($username)) {
                return Response::send(-1, '请输入用户');
            }
            if (empty($password)) {
                return Response::send(-1, '请输入密码');
            }

            if (ConfigFacade::get('loginCaptchaEnable', false)) {
                $captcha = Input::get('captcha');
                if (!Captcha::check($captcha)) {
                    return Response::send(-1, '验证码错误', null, '[js]$("[data-captcha]").click();');
                }
            }

            $memberUser = null;
            if (!$memberUser) {
                $ret = $memberService->login($username, null, null, $password);
                if (0 == $ret['code']) {
                    $memberUser = $ret['data'];
                }
            }
            if (!$memberUser) {
                $ret = $memberService->login(null, $username, null, $password);
                if (0 == $ret['code']) {
                    $memberUser = $ret['data'];
                }
            }
            if (!$memberUser) {
                $ret = $memberService->login(null, null, $username, $password);
                if (0 == $ret['code']) {
                    $memberUser = $ret['data'];
                }
            }
            if (!$memberUser) {
                return Response::send(-1, '登录失败', null, '[js]$("[data-captcha]").click();');
            }
            Session::put('memberUserId', $memberUser['id']);
            return Response::send(0, null, null, $redirect);
        }

        return $this->_view('login', [
            'redirect' => $redirect,
        ]);
    }

    public function loginCaptcha()
    {
        return Captcha::create('default');
    }

    public function retrieve()
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        return $this->_view('retrieve', compact('redirect'));
    }

    public function retrieveEmail(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        if (!ConfigFacade::get('retrieveEmailEnable', false)) {
            return Response::send(-1, '找回密码没有开启');
        }

        if (Request::isMethod('post')) {

            $email = Input::get('email');
            $verify = Input::get('verify');

            if (empty($email)) {
                return Response::send(-1, '邮箱不能为空');
            }
            if (!InputTypeHelper::isEmail($email)) {
                return Response::send(-1, '邮箱格式不正确');
            }
            if (empty($verify)) {
                return Response::send(-1, '验证码不能为空');
            }
            if ($verify != Session::get('retrieveEmailVerify')) {
                return Response::send(-1, '邮箱验证码不正确');
            }
            if (Session::get('retrieveEmailVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '邮箱验证码已过期');
            }
            if ($email != Session::get('retrieveEmail')) {
                return Response::send(-1, '两次邮箱不一致');
            }

            $memberUser = $memberService->loadByEmail($email);
            if (empty($memberUser)) {
                return Response::send(-1, '邮箱没有绑定任何账号');
            }

            Session::forget('retrieveEmailVerify');
            Session::forget('retrieveEmailVerifyTime');
            Session::forget('retrieveEmail');

            Session::put('retrieveMemberUserId', $memberUser['id']);

            return Response::send(0, null, null, '/retrieve/reset?redirect=' . urlencode($redirect));

        }

        return $this->_view('retrieveEmail', compact('redirect'));
    }

    public function retrieveEmailVerify(MemberService $memberService)
    {

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        $email = Input::get('target');
        if (empty($email)) {
            return Response::send(-1, '邮箱不能为空');
        }
        if (!InputTypeHelper::isEmail($email)) {
            return Response::send(-1, '邮箱格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUser = $memberService->loadByEmail($email);
        if (empty($memberUser)) {
            return Response::send(-1, '邮箱没有绑定任何账号');
        }

        if (Session::get('retrieveEmailVerifyTime') && $email == Session::get('retrieveEmail')) {
            if (Session::get('retrieveEmailVerifyTime') + 60 * 10 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('retrieveEmailVerify', $verify);
        Session::put('retrieveEmailVerifyTime', time());
        Session::put('retrieveEmail', $email);

        MailHelper::send($email, '找回密码验证码', MailTemplate::VERIFY, ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');
    }

    public function retrievePhone(MemberService $memberService)
    {

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (!ConfigFacade::get('retrievePhoneEnable', false)) {
            return Response::send(-1, '找回密码没有开启');
        }

        if (Request::isMethod('post')) {

            $phone = Input::get('phone');
            $verify = Input::get('verify');

            if (empty($phone)) {
                return Response::send(-1, '手机不能为空');
            }
            if (!InputTypeHelper::isPhone($phone)) {
                return Response::send(-1, '手机格式不正确');
            }
            if (empty($verify)) {
                return Response::send(-1, '验证码不能为空');
            }
            if ($verify != Session::get('retrievePhoneVerify')) {
                return Response::send(-1, '手机验证码不正确');
            }
            if (Session::get('retrievePhoneVerifyTime') + 60 * 60 < time()) {
                return Response::send(0, '手机验证码已过期');
            }
            if ($phone != Session::get('retrievePhone')) {
                return Response::send(-1, '两次手机不一致');
            }

            $memberUser = $memberService->loadByPhone($phone);
            if (empty($memberUser)) {
                return Response::send(-1, '手机没有绑定任何账号');
            }

            Session::forget('retrievePhoneVerify');
            Session::forget('retrievePhoneVerifyTime');
            Session::forget('retrievePhone');

            Session::put('retrieveMemberUserId', $memberUser['id']);

            return Response::send(0, null, null, '/retrieve/reset?redirect=' . urlencode($redirect));

        }

        return $this->_view('retrievePhone', compact('redirect'));
    }

    public function retrievePhoneVerify(MemberService $memberService)
    {

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        $phone = Input::get('target');
        if (empty($phone)) {
            return Response::send(-1, '手机不能为空');
        }
        if (!InputTypeHelper::isPhone($phone)) {
            return Response::send(-1, '手机格式不正确');
        }

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误');
        }

        $memberUser = $memberService->loadByPhone($phone);
        if (empty($memberUser)) {
            return Response::send(-1, '手机没有绑定任何账号');
        }

        if (Session::get('retrievePhoneVerifyTime') && $phone == Session::get('retrievePhone')) {
            if (Session::get('retrievePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::send(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        Session::put('retrievePhoneVerify', $verify);
        Session::put('retrievePhoneVerifyTime', time());
        Session::put('retrievePhone', $phone);

        SmsHelper::send($phone, SmsTemplate::VERIFY, ['verify' => $verify]);

        return Response::send(0, '验证码发送成功');
    }

    public function retrieveReset(MemberService $memberService)
    {

        if (ConfigFacade::get('retrieveDisable', false)) {
            return Response::send(-1, '找回密码已禁用');
        }

        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        $retrieveMemberUserId = Session::get('retrieveMemberUserId');
        if (empty($retrieveMemberUserId)) {
            return Response::send(0, null, null, '/retrieve');
        }
        $memberUser = $memberService->load($retrieveMemberUserId);
        if (empty($memberUser)) {
            return Response::send(0, null, null, '/retrieve');
        }

        if (Request::isMethod('post')) {
            $password = Input::get('password');
            $passwordRepeat = Input::get('passwordRepeat');
            if (empty($password)) {
                return Response::send(-1, '请输入密码');
            }
            if ($password != $passwordRepeat) {
                return Response::send(-1, '两次输入密码不一致');
            }
            $ret = $memberService->changePassword($memberUser['id'], $password, null, true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            Session::forget('retrieveMemberUserId');
            return Response::send(0, '成功设置新密码,请您登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('retrieveReset', compact('memberUser', 'redirect'));
    }

    public function retrieveCaptcha()
    {
        return Captcha::create('default');
    }

    public function logout()
    {
        if (ConfigFacade::get('ssoClientEnable', false)) {
            if (Input::get('server', '') != 'true') {
                $ssoServer = ConfigFacade::get('ssoServer', '');
                if (empty($ssoServer)) {
                    return Response::send(-1, '请配置 同步登录服务端地址');
                }
                $clientRedirect = Input::get('redirect', '/');
                $clientLogout = RequestHelper::domainUrl() . '/logout?server=true&redirect=' . urlencode($clientRedirect);
                $ssoServerLogout = $ssoServer . '_logout?redirect=' . urlencode($clientLogout);
                return Response::send(0, null, null, $ssoServerLogout);
            }
        }

        Session::forget('memberUserId');
        Session::forget('memberId');
        $redirect = Input::get('redirect', '/');
        return Response::send(0, null, null, $redirect);
    }

    private function getOauthConfig($type)
    {
        $config = [
            'APP_KEY' => null,
            'APP_SECRET' => null,
            'CALLBACK' => Response::schema() . '://' . Request::server('HTTP_HOST') . '/oauth_callback_' . $type,
        ];
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
                if (!OauthHelper::isWechatMobileEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigEnvHelper::get('oauthWechatMobileAppId');
                $config['APP_SECRET'] = ConfigEnvHelper::get('oauthWechatMobileAppSecret');
                return $config;
            case OauthType::QQ:
                if (!OauthHelper::isQQEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigEnvHelper::get('oauthQQKey');
                $config['APP_SECRET'] = ConfigEnvHelper::get('oauthQQAppSecret');
                return $config;
            case OauthType::WEIBO:
                if (!OauthHelper::isWeiboEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigEnvHelper::get('oauthWeiboKey');
                $config['APP_SECRET'] = ConfigEnvHelper::get('oauthWeiboAppSecret');
                return $config;
            case OauthType::WECHAT:
                if (!OauthHelper::isWechatEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigEnvHelper::get('oauthWechatAppId');
                $config['APP_SECRET'] = ConfigEnvHelper::get('oauthWechatAppSecret');
                return $config;
        }
        return null;
    }

    public function oauthWechatProxy()
    {
        return view('tecmz::util.oauthWechatProxy');
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

        $oauthWechatProxy = ConfigEnvHelper::get('oauthWechatMobileProxy');
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
            return Response::send(-1, '登录失败(code为空)', null, '/');
        }

        $config = $this->getOauthConfig($type);
        if (empty($config)) {
            return Response::send(-1, '授权登录配置错误', null, '/');
        }

        $oauth = Oauth::getInstance($type, $config);

        $token = null;
        $openid = null;
        try {
            $token = $oauth->getAccessToken($code, null);
            $openid = $oauth->openid();
        } catch (\Exception $e) {
            return Response::send(-1, '登录失败(' . $e->getMessage() . ')', null, '/');
        }

        if (empty($token) || empty($openid)) {
            return Response::send(-1, '登录失败(token=' . print_r($token, true) . ',openid=' . $openid . ')', null, '/');
        }

        $userInfo = [];

        switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                $data = $oauth->call('sns/userinfo');
                if (!empty($data ['errcode'])) {
                    return Response::send(-1, "微信登录失败：" . $data['errmsg'], null, '/');
                }
                $userInfo['username'] = $data['nickname'];
                $userInfo['avatar'] = $data['headimgurl'];
                $userInfo['unionId'] = empty($data['unionid']) ? null : $data['unionid'];
                break;
            case OauthType::QQ:
                $data = $oauth->call('user/get_user_info');
                if (!isset($data['ret']) || $data['ret'] != 0) {
                    return Response::send(-1, 'QQ登录失败:' . json_encode($data), null, '/');
                }
                $userInfo['username'] = $data['nickname'];
                foreach (['figureurl_qq_2', 'figureurl_2', 'figureurl_qq_1', 'figureurl_1', 'figureurl'] as $avatarField) {
                    if (isset($data[$avatarField]) && $data[$avatarField]) {
                        $userInfo['avatar'] = $data[$avatarField];
                        break;
                    }
                }
                break;
            case OauthType::WEIBO:
                $data = $oauth->call('users/show', "uid=" . $openid);
                if (!isset($data ['screen_name']) || empty($data ['screen_name'])) {
                    return Response::send(-1, '微博登录失败:' . json_encode($data), null, '/');
                }
                $userInfo['username'] = $data['screen_name'];
                $userInfo['avatar'] = empty($data['profile_image_url']) ? null : $data['profile_image_url'];
                break;
        }
        if (empty($userInfo)) {
            return Response::send(-1, '获取用户信息失败');
        }

        Session::put('oauthOpenId', $openid);
        Session::put('oauthUserInfo', $userInfo);

        if ($type == OauthType::WECHAT) {
            return '<script>window.parent.location.href="/oauth_bind_' . $type . '";</script>';
        }
        return Response::send(0, null, null, '/oauth_bind_' . $type);
    }

    public function oauthBind(MemberService $memberService,
                              $type)
    {
        //return $this->_view('oauthBind', compact('oauthOpenId', 'oauthUserInfo', 'redirect'));

        $redirect = Session::get('oauthRedirect', '/');
        $oauthOpenId = Session::get('oauthOpenId', null);
        $oauthUserInfo = Session::get('oauthUserInfo', null);
        if (empty($oauthOpenId) || empty($oauthUserInfo)) {
            return Response::send(-1, '用户授权数据为空', null, '/');
        }

        //如果用户已经登录直接关联到当前用户
        if ($this->memberUserId()) {
            switch ($type) {
                case OauthType::WECHAT_MOBILE:
                case OauthType::WECHAT:
                    if (!empty($oauthUserInfo['unionId'])) {
                        // 有开放平台关联微信手机登录和微信Web登录
                        $memberUserId = $memberService->getMemberUserIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                        if ($memberUserId && $this->memberUserId() != $memberUserId) {
                            $memberService->forgetOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                        }
                        $memberService->putOauth($this->memberUserId(), OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    }
                    $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
                        $memberService->forgetOauth($type, $oauthOpenId);
                    }
                    $memberService->putOauth($this->memberUserId(), $type, $oauthOpenId);
                    break;
                case OauthType::QQ:
                case OauthType::WEIBO:
                    $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
                        $memberService->forgetOauth($type, $oauthOpenId);
                    }
                    $memberService->putOauth($this->memberUserId(), $type, $oauthOpenId);
                    break;
            }
            Session::forget('oauthRedirect');
            Session::forget('oauthOpenId');
            Session::forget('oauthUserInfo');
            return Response::send(0, null, null, $redirect);
        }

        // 查看用户是否已经登录
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                if (!empty($oauthUserInfo['unionId'])) {
                    // 有开放平台关联微信手机登录和微信Web登录
                    $memberUserId = $memberService->getMemberUserIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    if ($memberUserId) {
                        $memberService->putOauth($memberUserId, $type, $oauthOpenId);
                        Session::put('memberUserId', $memberUserId);
                        Session::forget('oauthRedirect');
                        Session::forget('oauthOpenId');
                        Session::forget('oauthUserInfo');
                        return Response::send(0, null, null, $redirect);
                    }
                }
                $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    Session::put('memberUserId', $memberUserId);
                    Session::forget('oauthRedirect');
                    Session::forget('oauthOpenId');
                    Session::forget('oauthUserInfo');
                    return Response::send(0, null, null, $redirect);
                }
                break;
            case OauthType::QQ:
            case OauthType::WEIBO:
                $memberUserId = $memberService->getMemberUserIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    Session::put('memberUserId', $memberUserId);
                    Session::forget('oauthRedirect');
                    Session::forget('oauthOpenId');
                    Session::forget('oauthUserInfo');
                    return Response::send(0, null, null, $redirect);
                }
                break;
        }

        if (Request::isMethod('post')) {
            if (ConfigFacade::get('registerDisable', false)) {
                return Response::send(-1, '用户注册已禁用');
            }
            $username = Input::get('username');
            $ret = $memberService->register($username, null, null, null, true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));
            switch ($type) {
                case OauthType::WECHAT_MOBILE:
                case OauthType::WECHAT:
                    if (!empty($oauthUserInfo['unionId'])) {
                        $memberService->putOauth($memberUserId, OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    }
                    $memberService->putOauth($memberUserId, $type, $oauthOpenId);
                    break;
                case OauthType::QQ:
                case OauthType::WEIBO:
                    $memberService->putOauth($memberUserId, $type, $oauthOpenId);
                    break;
                default:
                    return Response::send(-1, 'oauthType error');
            }

            if (!empty($oauthUserInfo['avatar'])) {
                $avatarExt = FileHelper::extension($oauthUserInfo['avatar']);
                $avatar = CurlHelper::getContent($oauthUserInfo['avatar']);
                if (!empty($avatar)) {
                    if (empty($avatarExt)) {
                        $avatarExt = 'jpg';
                    }
                    $memberService->setAvatar($memberUserId, $avatar, $avatarExt);
                }
            }

            Session::put('memberUserId', $memberUserId);
            Session::forget('oauthRedirect');
            Session::forget('oauthOpenId');
            Session::forget('oauthUserInfo');
            return Response::send(0, null, null, $redirect);
        }

        return $this->_view('oauthBind', compact('oauthOpenId', 'oauthUserInfo', 'redirect'));
    }


    /**
     * 请弃用这个方法
     * @param MemberService $memberService
     * @return null
     * @deprecated
     */
    public function registerByEmail(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '网站禁止注册');
        }

        if (Request::isMethod('post')) {
            $email = Input::get('email');
            $password = Input::get('password');
            $passwordRepeat = Input::get('passwordRepeat');
            if (empty($email)) {
                return Response::send(-1, '请输入用邮箱');
            }
            if (empty($password)) {
                return Response::send(-1, '请输入密码');
            }
            if ($password != $passwordRepeat) {
                return Response::send(-1, '两次输入密码不一致');
            }
            $ret = $memberService->register(null, null, $email, $password);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));
            return Response::send(0, '注册成功，请您登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('register', compact('redirect'));
    }

    /**
     * 请弃用这个方法
     * @param MemberService $memberService
     * @return null
     * @deprecated
     */
    public function registerByUsernameEmail(MemberService $memberService)
    {
        $redirect = Input::get('redirect', '/member');
        if ($this->memberUserId()) {
            return Response::send(0, null, null, $redirect);
        }

        if (ConfigFacade::get('registerDisable', false)) {
            return Response::send(-1, '网站禁止注册');
        }

        if (Request::isMethod('post')) {
            $username = Input::get('username');
            $email = Input::get('email');
            $password = Input::get('password');
            $passwordRepeat = Input::get('passwordRepeat');
            if (empty($username)) {
                return Response::send(-1, '请输入用户名');
            }
            // 为了兼容使用使用统一登录
            if (Str::contains($username, '@')) {
                return Response::send(-1, '用户名不能包含特殊字符');
            }
            if (preg_match('/^\\d{11}$/', $username)) {
                return Response::send(-1, '用户名不能为纯数字');
            }
            if (empty($email)) {
                return Response::send(-1, '请输入邮箱');
            }
            if (empty($password)) {
                return Response::send(-1, '请输入密码');
            }
            if ($password != $passwordRepeat) {
                return Response::send(-1, '两次输入密码不一致');
            }
            $ret = $memberService->register($username, null, $email, $password);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            $memberUserId = $ret['data']['id'];
            Event::fire(new MemberUserRegisteredEvent($memberUserId));

            return Response::send(0, '注册成功,请您登录', null, '/login?redirect=' . urlencode($redirect));
        }

        return $this->_view('register', compact('redirect'));
    }

    public function tokenLogin(ApiSessionService $apiSessionService)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (empty($redirect)) {
            $redirect = '/';
        }
        $apiToken = $input->getTrimString('token', '');
        if (empty($apiToken)) {
            return Response::send(-1, '缺少ApiToken');
        }
        $memberUserId = $apiSessionService->get('memberUserId', 0, $apiToken);
        if (empty($memberUserId)) {
            return Response::send(-1, '登录失败');
        }
        Session::put('memberUserId', $memberUserId);
        return Response::send(0, null, null, $redirect);
    }

    public function tokenBindOauth(ApiSessionService $apiSessionService,
                                   MemberService $memberService)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (empty($redirect)) {
            $redirect = '/';
        }
        $apiToken = $input->getTrimString('token', '');
        if (empty($apiToken)) {
            return Response::send(-1, '缺少ApiToken');
        }
        $tokenBindOauthType = $apiSessionService->get('tokenBindOauthType', '', $apiToken);
        $tokenBindOauthOpenId = $apiSessionService->get('tokenBindOauthOpenId', '', $apiToken);
        if (empty($tokenBindOauthType) || empty($tokenBindOauthOpenId)) {
            return Response::send(-1, '错误的绑定信息');
        }
        $memberUserId = Session::get('memberUserId', 0);
        if (empty($memberUserId)) {
            return Response::send(-1, '用户未登录');
        }
        $memberService->forgetOauth($tokenBindOauthType, $tokenBindOauthOpenId);
        $memberService->putOauth($memberUserId, $tokenBindOauthType, $tokenBindOauthOpenId);
        return Response::send(0, null, null, $redirect);
    }

}