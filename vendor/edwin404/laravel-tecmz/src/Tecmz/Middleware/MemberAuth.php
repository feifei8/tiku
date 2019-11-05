<?php

namespace Edwin404\Tecmz\Middleware;


use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Member\Support\MemberLoginCheck;

class MemberAuth extends \Edwin404\Member\Middleware\MemberAuth
{
    protected function check($controller, $action, $memberUser)
    {
        if (is_subclass_of($controller, MemberLoginCheck::class)) {
            if (empty($memberUser['id'])) {
                if (property_exists($controller, 'ignoreAction')
                    && is_array($controller::$ignoreAction)
                    && in_array($action, $controller::$ignoreAction)
                ) {
                    //pass
                } else {
                    $ret = Response::send(-1, null, null, '/login?redirect=' . urlencode(RequestHelper::currentPageUrl()));
                    return Response::generate(-1, null, $ret);
                }
            }
        }
        return Response::generate(0, 'ok');
    }

}