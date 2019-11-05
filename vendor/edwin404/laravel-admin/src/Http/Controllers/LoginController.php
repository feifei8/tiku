<?php

namespace Edwin404\Admin\Http\Controllers;

use Edwin404\Admin\Facades\AdminUserFacade;
use Edwin404\Admin\Http\Controllers\Support\AdminAwareController;
use Edwin404\Admin\Models\AdminUser;
use Edwin404\Admin\Services\AdminUserService;
use Edwin404\Base\Support\ModelHelper;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use EdwinFound\Utils\StrUtil;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Mews\Captcha\Facades\Captcha;

class LoginController extends AdminAwareController
{
    public function flush()
    {
        ModelHelper::update(AdminUser::class, [], ['ruleChanged' => true]);
        return Response::json(0, 'login flush success');
    }

    public function logout()
    {
        Session::flush();
        if (ConfigFacade::get('adminSSOClientEnable', false)) {
            if (Input::get('server', '') != 'true') {
                $ssoServer = ConfigFacade::get('adminSSOServer', '');
                if (empty($ssoServer)) {
                    return Response::send(-1, '请配置 同步登录服务端地址');
                }
                $clientRedirect = Input::get('redirect', '/');
                $clientLogout = RequestHelper::domainUrl() . '/logout?server=true&redirect=' . urlencode($clientRedirect);
                $ssoServerLogout = $ssoServer . '_logout?redirect=' . urlencode($clientLogout);
                return Response::send(0, null, null, $ssoServerLogout);
            }
        }
        return Response::send(0, null, null, env('ADMIN_PATH', '/'));
    }

    public function index(AdminUserService $adminUserService)
    {
        $redirect = Input::get('redirect', env('ADMIN_PATH', '/admin/'));

        if ($this->adminUserId()) {
            return Response::send(0, '您已经登录', null, $redirect);
        }

        if (ConfigFacade::get('adminSSOClientEnable', false)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'sso/client?redirect=' . urlencode($redirect));
        }

        if (Request::isMethod('post')) {

            $username = trim(Input::get('username'));
            $password = trim(Input::get('password'));

            if (empty($username)) {
                return Response::send(-1, '用户名为空');
            }
            if (empty($password)) {
                return Response::send(-2, '密码为空');
            }

            if (config('admin.login.captcha')) {
                $captcha = Input::get('captcha');
                if (!Captcha::check($captcha)) {
                    return Response::send(-1, '图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
                }
            }

            $ret = $adminUserService->login($username, $password);
            if ($ret['code']) {
                AdminUserFacade::addErrorLog(0, '登录错误', [
                    'IP' => Request::ip(),
                    '用户名' => $username,
                    '密码' => StrUtil::mask($password),
                ]);
                return Response::send(-1, '用户或密码错误:' . $ret['code'], null, '[js]$(\'[data-captcha]\').click();');
            }

            $user = $ret['data'];
            Session::put('_adminUserId', $user['id']);

            AdminUserFacade::addInfoLog($user['id'], '登录成功', [
                'IP' => Request::ip(),
            ]);

            $redirect = Input::get('redirect', env('ADMIN_PATH', '/admin/'));
            return Response::send(0, null, null, $redirect);
        }
        return view('admin::login');
    }

    public function captcha()
    {
        return Captcha::create('default');
    }

    public function ssoClient(AdminUserService $adminUserService)
    {
        if (!ConfigFacade::get('adminSSOClientEnable', false)) {
            return Response::send(-1, '请开启 同步登录客户端');
        }

        $ssoServer = ConfigFacade::get('adminSSOServer', '');
        if (empty($ssoServer)) {
            return Response::send(-1, '请配置 同步登录服务端地址');
        }

        $ssoSecret = ConfigFacade::get('adminSSOClientSecret');
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

            $adminUser = $adminUserService->loadByUsername($username);
            if (empty($adminUser)) {
                $adminUser = $adminUserService->add($username, null, true);
                $adminUser = $adminUserService->load($adminUser['id']);
            }

            Session::put('_adminUserId', $adminUser['id']);

            $ssoRedirect = Session::get('adminSSORedirect', null);
            if (empty($ssoRedirect)) {
                return Response::send(0, '已经登录成功 但是没有找到跳转地址');
            }
            return Response::send(0, null, null, $ssoRedirect);

        } else {

            $redirect = trim(Input::get('redirect'));
            Session::put('adminSSORedirect', $redirect);

            $client = RequestHelper::domainUrl() . env('ADMIN_PATH', '/admin/') . 'sso/client';
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
        if (!ConfigFacade::get('adminSSOServerEnable', false)) {
            return Response::send(-1, '请开启 同步登录服务端');
        }
        $ssoSecret = ConfigFacade::get('adminSSOServerSecret');
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
        $ssoClientList = explode("\n", ConfigFacade::get('adminSSOClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::send(-1, '请在 同步登录服务端增加客户端地址 ' . $client);
        }
        Session::put('adminSSOClient', $client);

        if (Session::get('_adminUserId', 0)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'sso/server_success');
        }

        return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'login?redirect=' . urlencode(env('ADMIN_PATH', '/admin/') . 'sso/server_success'));
    }

    public function ssoServerSuccess(AdminUserService $adminUserService)
    {
        if (!Session::get('_adminUserId', 0)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'login?redirect=' . urlencode(env('ADMIN_PATH', '/admin/') . 'sso/server_success'));
        }
        $adminUser = $adminUserService->load(Session::get('_adminUserId', 0));

        $ssoSecret = ConfigFacade::get('adminSSOServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录服务端通讯秘钥');
        }

        $server = RequestHelper::domainUrl() . env('ADMIN_PATH', '/admin/') . 'sso/server';
        $timestamp = time();
        $username = $adminUser['username'];
        $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $ssoClient = Session::get('adminSSOClient', '');
        if (empty($ssoClient)) {
            return Response::send(0, '登录成功但是没有找到客户端');
        }
        Session::forget('adminSSOClient', $ssoClient);

        $redirect = $ssoClient . '?server=' . urlencode($server) . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username)) . '&sign=' . $sign;

        return Response::send(0, null, null, $redirect);
    }

    public function ssoServerLogout()
    {
        Session::forget('_adminUserId');
        $redirect = Input::get('redirect', env('ADMIN_PATH', '/admin/'));
        return Response::send(0, null, null, $redirect);
    }
}