<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Common\Helpers\AgentHelper;
use Edwin404\Config\Facades\ConfigFacade;

class OauthHelper
{
    public static function hasOauth()
    {
        if (self::isWechatMobileEnable()) {
            return true;
        }
        if (self::isQQEnable()) {
            return true;
        }
        if (self::isWeiboEnable()) {
            return true;
        }
        if (self::isWechatEnable()) {
            return true;
        }
        if (self::isWechatMiniProgramEnable()) {
            return true;
        }
        return false;
    }

    public static function isWechatMobileEnable()
    {
        if (!AgentHelper::isWechat()) {
            return false;
        }
        if (ConfigFacade::get('oauthWechatMobileEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatMiniProgramEnable()
    {
        if (!AgentHelper::isWechat()) {
            return false;
        }
        if (ConfigFacade::get('oauthWechatMiniProgramEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isQQEnable()
    {
        if (ConfigFacade::get('oauthQQEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatEnable()
    {
        if (AgentHelper::isMobile()) {
            return false;
        }
        if (ConfigFacade::get('oauthWechatEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWeiboEnable()
    {
        if (ConfigFacade::get('oauthWeiboEnable', false)) {
            return true;
        }
        return false;
    }
}