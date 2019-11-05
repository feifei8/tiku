<?php

namespace Edwin404\Tecmz\Helpers;


use Edwin404\Common\Helpers\AgentHelper;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Pay\Types\PayType;

class PayHelper
{
    public static function hasPay()
    {
        if (self::isWechatMobileEnable()) {
            return true;
        }
        if (self::isAlipayEnable()) {
            return true;
        }
        if (self::isAlipayWebEnable()) {
            return true;
        }
        if (self::isWechatEnable()) {
            return true;
        }
        if (self::isAlipayManualEnable()) {
            return true;
        }
        if (self::isWechatManualEnable()) {
            return true;
        }
        if (self::isOfflinePayEnable()) {
            return true;
        }
        return false;
    }

    public static function isPayEnable($payType)
    {
        switch ($payType) {
            case PayType::ALIPAY:
                return self::isAlipayEnable();
            case PayType::ALIPAY_WEB:
                return self::isAlipayWebEnable();
            case PayType::WECHAT_MOBILE:
                return self::isWechatMobileEnable();
            case PayType::WECHAT_MINI_PROGRAM:
                return self::isWechatMiniProgramEnable();
            case PayType::WECHAT:
                return self::isWechatEnable();
            case PayType::ALIPAY_MANUAL:
                return self::isAlipayManualEnable();
            case PayType::WECHAT_MANUAL:
                return self::isWechatManualEnable();
            case PayType::OFFLINE_PAY:
                return self::isOfflinePayEnable();
        }
        return false;
    }

    public static function isWechatMobileEnable()
    {
        if (AgentHelper::isWechat()) {
            if (ConfigEnvHelper::get('payWechatMobileOn', false)) {
                return true;
            }
        }
        return false;
    }

    public static function isWechatMiniProgramEnable()
    {
        if (AgentHelper::isWechat()) {
            if (ConfigEnvHelper::get('payWechatMiniProgramOn', false)) {
                return true;
            }
        }
        return false;
    }

    public static function isWechatEnable()
    {
        if (AgentHelper::isMobile()) {
            return false;
        }
        if (ConfigFacade::get('payWechatOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayEnable()
    {
        if (AgentHelper::isWechat()) {
            return false;
        }
        if (env('PAY_ALIPAY_ON', false) || ConfigFacade::get('payAlipayOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayManualEnable()
    {
        if (ConfigFacade::get('payAlipayManualOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayWebEnable()
    {
        if (ConfigFacade::get('payAlipayWebOn', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatManualEnable()
    {
        if (ConfigFacade::get('payWechatManualOn', false)) {
            return true;
        }
        return false;
    }

    public static function isOfflinePayEnable()
    {
        if (ConfigFacade::get('payOfflinePayOn', false)) {
            return true;
        }
        return false;
    }
}