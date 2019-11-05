<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Member\Facades\MemberMessageFacade;
use Illuminate\Support\Facades\View;

class MemberMessageHelper
{
    public static function send($memberUserId, $template, $templateData = [], $fromMemberUserId = 0)
    {
        $view = 'theme.' . ConfigFacade::get('siteTemplate', 'default') . '.message.' . $template;
        if (!view()->exists($view)) {
            $view = 'theme.default.message.' . $template;
        }

        if (!view()->exists($view)) {
            throw new \Exception('message view not found : ' . $view);
        }

        $message = View::make($view, $templateData)->render();
        MemberMessageFacade::send($memberUserId, $message, $fromMemberUserId);
    }
}