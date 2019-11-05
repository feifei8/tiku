<?php

namespace Edwin404\Admin\Helpers;


use Edwin404\Admin\Facades\AdminUserFacade;
use Illuminate\Support\Facades\Session;

class AdminLogHelper
{
    public static function addInfoLogIfChanged($summary, $old, $new)
    {
        AdminUserFacade::addInfoLogIfChanged(intval(Session::get('_adminUserId', null)), $summary, $old, $new);
    }

    public static function addInfoLog($summary, $content = [])
    {
        AdminUserFacade::addInfoLog(intval(Session::get('_adminUserId', null)), $summary, $content);
    }
}