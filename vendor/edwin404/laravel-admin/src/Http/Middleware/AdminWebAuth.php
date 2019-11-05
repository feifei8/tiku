<?php

namespace Edwin404\Admin\Http\Middleware;

use Closure;
use Edwin404\Admin\Helpers\AdminPowerHelper;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Admin\Services\AdminUserService;
use Edwin404\Base\Support\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminWebAuth
{
    private $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeAction = Route::currentRouteAction();
        $pieces = explode('@', $routeAction);
        if (isset($pieces[0])) {
            $controller = $pieces[0];
        } else {
            $controller = null;
        }
        if (isset($pieces[1])) {
            $action = $pieces[1];
        } else {
            $action = null;
        }
        if (!Str::startsWith($controller, '\\')) {
            $controller = '\\' . $controller;
        }

        $adminUserId = intval(Session::get('_adminUserId', null));
        $adminUser = null;
        if ($adminUserId) {
            $adminUser = $this->adminUserService->load($adminUserId);
        }

        if ($adminUserId && !$adminUser) {
            Session::forget('_adminUserId');
            return Response::send(-1, '请登录', null, action('\Edwin404\Admin\Http\Controllers\LoginController@index', ['redirect' => Request::url()]));
        }

        if (is_subclass_of($controller, AdminCheckController::class)) {
            if (empty($adminUser)) {
                return Response::send(-1, null, null, action('\Edwin404\Admin\Http\Controllers\LoginController@index', ['redirect' => Request::url()]));
            }
        }
        Session::flash('_adminUser', $adminUser);

        // 检测权限

        $rules = [];
        foreach (AdminPowerHelper::rules('powers') as $rule) {
            $rules[$rule] = false;
        }

        if ($adminUser['id'] == 1) {
            foreach ($rules as $rule => $index) {
                $rules[$rule] = true;
            }
        } else {
            $adminHasRules = Session::get('_adminHasRules', []);
            if ((empty($adminHasRules) && $adminUser['id'] > 0) || $adminUser['ruleChanged']) {
                if ($adminUser['ruleChanged']) {
                    $this->adminUserService->ruleChanged($adminUser['id'], false);
                }
                $adminHasRules = [];
                $ret = $this->adminUserService->getRolesByUserId($adminUser['id']);
                foreach ($ret['data'] as $role) {
                    foreach ($role['rules'] as $rule) {
                        $adminHasRules[$rule['rule']] = true;
                    }
                }
                Session::put('_adminHasRules', $adminHasRules);
            }
            foreach ($adminHasRules as $rule => $v) {
                $rules[$rule] = true;
            }
        }

        Session::put('_adminRules', $rules);

        if (isset($rules[$controller . '@' . $action]) && !$rules[$controller . '@' . $action]) {
            return Response::send(-1, '没有权限');
        }

        return $next($request);
    }

}