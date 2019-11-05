<?php

namespace Edwin404\Tecmz\Helpers;


use EasyWeChat\Foundation\Application;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Wechat\Types\WechatAuthType;

class WechatHelper
{
    public static function oauthMobileApp()
    {
        if (!ConfigFacade::get('oauthWechatMobileEnable')) {
            return null;
        }
        return \Edwin404\Wechat\Helpers\WechatHelper::app(0, [
            'enable' => true,
            'appId' => ConfigFacade::get('oauthWechatMobileAppId', ''),
            'appSecret' => ConfigFacade::get('oauthWechatMobileAppSecret', ''),
            'appToken' => '',
            'appEncodingKey' => '',
            'authType' => WechatAuthType::CONFIG,
        ], ['payment' => false,]);
    }

    public static function shareApp()
    {
        if (!ConfigFacade::get('shareWechatMobileEnable')) {
            return null;
        }
        return \Edwin404\Wechat\Helpers\WechatHelper::app(0, [
            'enable' => true,
            'appId' => ConfigFacade::get('shareWechatMobileAppId', ''),
            'appSecret' => ConfigFacade::get('shareWechatMobileAppSecret', ''),
            'appToken' => '',
            'appEncodingKey' => '',
            'authType' => WechatAuthType::CONFIG,
        ], ['payment' => false,]);
    }

    public static function wxApp()
    {
        if (!ConfigFacade::get('wxappEnable')) {
            return null;
        }
        $options = [
            // ...
            'mini_program' => [
                'app_id' => ConfigFacade::get('wxappAppId', ''),
                'secret' => ConfigFacade::get('wxappAppSecret', ''),
                // token 和 aes_key 开启消息推送后可见
                'token' => '',
                'aes_key' => ''
            ],
            // ...
        ];
        return new Application($options);
    }

}