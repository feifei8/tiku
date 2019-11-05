<?php

namespace Edwin404\Tecmz\Controllers;


use Edwin404\Admin\Cms\Field\FieldImage;
use Edwin404\Admin\Cms\Field\FieldSelect;
use Edwin404\Admin\Cms\Field\FieldSwitch;
use Edwin404\Admin\Cms\Field\FieldText;
use Edwin404\Admin\Cms\Field\FieldTextarea;
use Edwin404\Admin\Cms\Handle\ConfigCms;
use Edwin404\Admin\Http\Controllers\Support\AdminCheckController;
use Edwin404\Base\Support\InputPackage;
use Edwin404\Base\Support\RequestHelper;
use Edwin404\Base\Support\Response;
use Edwin404\Config\Facades\ConfigFacade;
use Edwin404\Config\Services\ConfigService;
use Edwin404\Data\Types\WatermarkType;
use Edwin404\Html\HtmlType;
use Edwin404\Tecmz\Helpers\MailHelper;
use Edwin404\Tecmz\Types\MemberRegisterType;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ConfigController extends AdminCheckController
{
    public function setting(ConfigCms $configCms, $param = [])
    {
        if (empty($param['siteTemplateOptions'])) {
            $param['siteTemplateOptions'] = [
                'default' => '默认模板',
            ];
        }

        return $configCms->execute($this, [
            'group' => 'setting',
            'pageTitle' => '基本配置',
            'fields' => [
                'siteLogo' => ['type' => FieldImage::class, 'title' => '网站Logo', 'desc' => ''],
                'siteName' => ['type' => FieldText::class, 'title' => '网站名称', 'desc' => ''],
                'siteSlogan' => ['type' => FieldText::class, 'title' => '网站副标题', 'desc' => ''],
                'siteDomain' => ['type' => FieldText::class, 'title' => '网站域名', 'desc' => ''],
                'siteKeywords' => ['type' => FieldText::class, 'title' => '网站关键词', 'desc' => ''],
                'siteDescription' => ['type' => FieldTextarea::class, 'title' => '网站描述', 'desc' => ''],
                'siteBeian' => ['type' => FieldText::class, 'title' => '网站备案编号', 'desc' => ''],
                'siteFavIco' => ['type' => FieldImage::class, 'title' => '网站ICO', 'desc' => '',],
                'siteTemplate' => ['type' => FieldSelect::class, 'title' => '网站模板', 'desc' => '', 'options' => $param['siteTemplateOptions']],
            ]
        ]);
    }

    public function email(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'email',
            'pageTitle' => '邮件发送',
            'fields' => [
                'systemEmailEnable' => ['type' => FieldSwitch::class, 'title' => '开启邮件发送', 'desc' => ''],
                'systemEmailSmtpServer' => ['type' => FieldText::class, 'title' => 'SMTP服务器地址', 'desc' => ''],
                'systemEmailSmtpSsl' => ['type' => FieldSwitch::class, 'title' => 'SMTP是否为SSL', 'desc' => ''],
                'systemEmailSmtpUser' => ['type' => FieldText::class, 'title' => 'SMTP用户', 'desc' => ''],
                'systemEmailSmtpPassword' => ['type' => FieldText::class, 'title' => 'SMTP密码', 'desc' => ''],
            ]
        ]);
    }

    public function emailTest(ConfigCms $configCms)
    {
        if (RequestHelper::isPost()) {
            $email = InputPackage::buildFromInput()->getEmail('email');
            if (empty($email)) {
                return Response::send(-1, '邮箱为空或格式不正确');
            }
            config([
                'mail' => [
                    'driver' => 'smtp',
                    'host' => ConfigFacade::get('systemEmailSmtpServer'),
                    'port' => ConfigFacade::get('systemEmailSmtpSsl', false) ? 465 : 25,
                    'encryption' => ConfigFacade::get('systemEmailSmtpSsl', false) ? 'ssl' : 'tls',
                    'from' => array('address' => ConfigFacade::get('systemEmailSmtpUser'), 'name' => ConfigFacade::get('siteName') . ' @ ' . ConfigFacade::get('siteDomain')),
                    'username' => ConfigFacade::get('systemEmailSmtpUser'),
                    'password' => ConfigFacade::get('systemEmailSmtpPassword'),
                ]
            ]);
            $emailUserName = $email;
            $subject = '测试邮件';
            try {
                Mail::send('tecmz::mail.test', [], function ($message) use ($email, $emailUserName, $subject) {
                    $message->to($email, $emailUserName)->subject($subject);
                });
            } catch (\Exception $e) {
                return Response::send(-1, '邮件发送失败:(' . $e->getMessage() . ')');
            }
            return Response::send(0, '测试邮件成功发送到' . $email);
        }
        return $configCms->execute($this, [
            'group' => 'emailTest',
            'pageTitle' => '邮件发送测试',
            'fields' => [
                'email' => ['type' => FieldText::class, 'title' => '测试接收邮箱', 'desc' => ''],
            ]
        ]);
    }

    public function contact(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'email',
            'pageTitle' => '联系方式',
            'fields' => [
                'contactPhone' => ['type' => FieldText::class, 'title' => '电话', 'desc' => ''],
                'contactEmail' => ['type' => FieldText::class, 'title' => '邮箱', 'desc' => ''],
                'contactQQ' => ['type' => FieldText::class, 'title' => 'QQ', 'desc' => ''],
                'contactWechat' => ['type' => FieldText::class, 'title' => '微信', 'desc' => ''],
                'contactWechatQrcode' => ['type' => FieldImage::class, 'title' => '微信二维码', 'desc' => ''],
                'contactWechatOfficialAccount' => ['type' => FieldText::class, 'title' => '微信公众号', 'desc' => ''],
                'contactWechatOfficialAccountQrcode' => ['type' => FieldImage::class, 'title' => '微信公众号二维码', 'desc' => ''],
                'contactSina' => ['type' => FieldText::class, 'title' => '新浪微博', 'desc' => ''],
                'contactSinaQrcode' => ['type' => FieldImage::class, 'title' => '新浪微博二维码', 'desc' => ''],
            ]
        ]);
    }

    public function sms(ConfigService $configService)
    {
        if (Request::isMethod('post')) {

            $configService->set('systemSmsEnable', intval(Input::get('systemSmsEnable')));

            $configService->set('systemSmsSender', trim(Input::get('systemSmsSender')));

            $configService->set('systemSmsSenderTecmzAppKey', trim(Input::get('systemSmsSenderTecmzAppKey')));
            $configService->set('systemSmsSenderTecmzVerifyTemplateId', trim(Input::get('systemSmsSenderTecmzVerifyTemplateId')));

            return Response::send(0, '保存成功');

        }

        return view('tecmz::admin.config.sms');
    }

    public function visit(ConfigCms $configCms)
    {
        if (Request::isMethod('post')) {
            $systemCdnUrl = Input::get('systemCdnUrl');
            if ($systemCdnUrl && !Str::endsWith($systemCdnUrl, '/')) {
                return Response::send(-1, '网站加速CDN必须以/结尾');
            }
        }

        return $configCms->execute($this, [
            'group' => 'visit',
            'pageTitle' => '访问设置',
            'fields' => [
                'systemCounter' => ['type' => FieldTextarea::class, 'title' => 'head访问统计代码', 'desc' => ''],
                'systemCounterBody' => ['type' => FieldTextarea::class, 'title' => 'body访问统计代码', 'desc' => ''],
                'systemCdnUrl' => ['type' => FieldText::class, 'title' => '网站加速CDN', 'desc' => '如 http://cdn.example.com/'],
            ]
        ]);
    }

    public function payAlipay(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payAlipay',
            'pageTitle' => '支付宝',
            'fields' => [
                'payAlipayOn' => ['type' => FieldSwitch::class, 'title' => '开启支付宝付款', 'desc' => ''],
                'payAlipayPartnerId' => ['type' => FieldText::class, 'title' => '卖家ID(PartnerId)', 'desc' => '如 2085364735263489'],
                'payAlipaySellerId' => ['type' => FieldText::class, 'title' => 'ID(SellerId)', 'desc' => '如 seller@example.com'],
                'payAlipayKey' => ['type' => FieldText::class, 'title' => '安全Key', 'desc' => '如 pdehgmdjeubnghtyddktjm174hdj'],
            ]
        ]);
    }

    public function payAlipayWeb(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payAlipayWeb',
            'pageTitle' => '支付宝-Web',
            'fields' => [
                'payAlipayWebOn' => ['type' => FieldSwitch::class, 'title' => '开启', 'desc' => ''],
                'payAlipayWebAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'payAlipayWebAliPublicKey' => ['type' => FieldText::class, 'title' => '支付宝公钥', 'desc' => ''],
                'payAlipayWebRSAPrivateKey' => ['type' => FieldTextarea::class, 'title' => 'RSA2(SHA256)密钥(推荐)', 'desc' => '复制 -----BEGIN RSA PRIVATE KEY----- 和 -----END RSA PRIVATE KEY----- 中间的部分'],
            ]
        ]);
    }

    public function payAlipayManual(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payAlipayManual',
            'pageTitle' => '支付宝手动付款',
            'fields' => [
                'payAlipayManualOn' => ['type' => FieldSwitch::class, 'title' => '开启支付宝手动付款', 'desc' => ''],
                'payAlipayManualQrcode' => ['type' => FieldImage::class, 'title' => '扫码支付二维码图片', 'desc' => ''],
            ]
        ]);
    }

    public function payWechat(ConfigCms $configCms)
    {
        if (Request::isMethod('post')) {
            file_exists($file = base_path('storage/cache/pay/wechat_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechat',
            'pageTitle' => '微信扫码支付',
            'fields' => [
                'payWechatOn' => ['type' => FieldSwitch::class, 'title' => '开启微信扫码支付', 'desc' => '只能在PC端使用微信支付'],
                'payWechatAppId' => ['type' => FieldText::class, 'title' => '微信扫码支付AppId', 'desc' => ''],
                'payWechatAppSecret' => ['type' => FieldText::class, 'title' => '微信扫码支付AppSecret', 'desc' => ''],
                'payWechatAppToken' => ['type' => FieldText::class, 'title' => '微信扫码支付AppToken', 'desc' => ''],
                'payWechatMerchantId' => ['type' => FieldText::class, 'title' => '微信扫码支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatKey' => ['type' => FieldText::class, 'title' => '微信扫码支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatFileCert' => ['type' => FieldTextarea::class, 'title' => '微信扫码支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatFileKey' => ['type' => FieldTextarea::class, 'title' => '微信扫码支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }


    public function payWechatMobile(ConfigCms $configCms)
    {
        if (Request::isMethod('post')) {
            file_exists($file = base_path('storage/cache/pay/wechat_mobile_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_mobile_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechat',
            'pageTitle' => '微信手机支付',
            'fields' => [
                'payWechatMobileOn' => ['type' => FieldSwitch::class, 'title' => '开启微信手机支付', 'desc' => '只能在微信中支付'],
                'payWechatMobileAppId' => ['type' => FieldText::class, 'title' => '微信手机支付AppId', 'desc' => ''],
                'payWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '微信手机支付AppSecret', 'desc' => ''],
                'payWechatMobileMerchantId' => ['type' => FieldText::class, 'title' => '微信手机支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatMobileKey' => ['type' => FieldText::class, 'title' => '微信手机支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatMobileFileCert' => ['type' => FieldTextarea::class, 'title' => '微信手机支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatMobileFileKey' => ['type' => FieldTextarea::class, 'title' => '微信手机支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }

    public function payWechatMiniProgram(ConfigCms $configCms)
    {
        if (Request::isMethod('post')) {
            file_exists($file = base_path('storage/cache/pay/wechat_mini_program_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_mini_program_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechatMiniProgram',
            'pageTitle' => '微信小程序支付',
            'fields' => [
                'payWechatMiniProgramOn' => ['type' => FieldSwitch::class, 'title' => '开启微信小程序支付', 'desc' => '只能在微信中支付'],
                'payWechatMiniProgramAppId' => ['type' => FieldText::class, 'title' => '微信小程序支付AppId', 'desc' => ''],
                'payWechatMiniProgramAppSecret' => ['type' => FieldText::class, 'title' => '微信小程序支付AppSecret', 'desc' => ''],
                'payWechatMiniProgramMerchantId' => ['type' => FieldText::class, 'title' => '微信小程序支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatMiniProgramKey' => ['type' => FieldText::class, 'title' => '微信小程序支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatMiniProgramFileCert' => ['type' => FieldTextarea::class, 'title' => '微信小程序支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatMiniProgramFileKey' => ['type' => FieldTextarea::class, 'title' => '微信小程序支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }

    public function payWechatManual(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payWechatManual',
            'pageTitle' => '微信手动付款',
            'fields' => [
                'payWechatManualOn' => ['type' => FieldSwitch::class, 'title' => '开启微信手动付款', 'desc' => ''],
                'payWechatManualQrcode' => ['type' => FieldImage::class, 'title' => '扫码支付二维码图片', 'desc' => ''],
            ]
        ]);
    }

    public function payOfflinePay(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payOfflinePay',
            'pageTitle' => '货到付款',
            'fields' => [
                'payOfflinePayOn' => ['type' => FieldSwitch::class, 'title' => '开启货到付款', 'desc' => ''],
            ]
        ]);
    }

    public function register(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'register',
            'pageTitle' => '注册设置',
            'fields' => [
                'registerDisable' => ['type' => FieldSwitch::class, 'title' => '禁止注册', 'desc' => '禁止注册后需要在后台手动增加账号才能使用'],
                'registerType' => ['type' => FieldSelect::class, 'title' => '注册方式', 'desc' => '', 'optionType' => MemberRegisterType::class],
            ]
        ]);
    }

    public function login(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'login',
            'pageTitle' => '登录设置',
            'fields' => [
                'loginCaptchaEnable' => ['type' => FieldSwitch::class, 'title' => '登录验证码', 'desc' => ''],
            ]
        ]);
    }

    public function oauthWechatServer(ConfigCms $configCms)
    {
        $descs = [];
        $descs[] = '<div style="line-height:2em;">';
        $descs[] = '配置信息参照如下';
        $descs[] = '登录授权的发起页域名:' . Request::server('HTTP_HOST');
        $descs[] = '授权事件接收URL:' . Response::schema() . '://' . Request::server('HTTP_HOST') . '/wx/notify';
        $descs[] = '公众号消息与事件接收URL:' . Response::schema() . '://' . Request::server('HTTP_HOST') . '/wx/handle/$APPID$';
        $descs[] = '公众号开发域名:' . Request::server('HTTP_HOST');
        $descs[] = '</div>';

        return $configCms->execute($this, [
            'group' => 'oauthWechatServer',
            'pageTitle' => '微信第三方平台设置',
            'fields' => [
                'wechatAuthorizationEnable' => ['type' => FieldSwitch::class, 'title' => '开启授权', 'desc' => ''],
                'wechatAuthorizationAppId' => ['type' => FieldText::class, 'title' => 'AppID', 'desc' => ''],
                'wechatAuthorizationAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
                'wechatAuthorizationToken' => ['type' => FieldText::class, 'title' => '公众号消息校验Token', 'desc' => ''],
                'wechatAuthorizationEncodingKey' => ['type' => FieldText::class, 'title' => '公众号消息加解密Key', 'desc' => join("<br/>", $descs)],
            ]
        ]);
    }

    public function oauthWechat(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthWechat',
            'pageTitle' => '微信授权登录',
            'fields' => [

                'oauthWechatMobileEnable' => ['type' => FieldSwitch::class, 'title' => '[手机] 开启微信授权登录', 'desc' => ''],
                'oauthWechatMobileProxy' => ['type' => FieldText::class, 'title' => '[手机] 授权回调域名代理', 'desc' => '如不清楚此参数意义,请留空'],
                'oauthWechatMobileAppId' => ['type' => FieldText::class, 'title' => '[手机] AppId', 'desc' => ''],
                'oauthWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '[手机] AppSecret', 'desc' => ''],

                'oauthWechatEnable' => ['type' => FieldSwitch::class, 'title' => '[PC端] 开启PC微信扫码登录', 'desc' => '回调域名请填写 <code>' . Request::server('HTTP_HOST') . '</code>'],
                'oauthWechatAppId' => ['type' => FieldText::class, 'title' => '[PC端] AppId', 'desc' => ''],
                'oauthWechatAppSecret' => ['type' => FieldText::class, 'title' => '[PC端] AppSecret', 'desc' => ''],

            ]
        ]);
    }


    public function oauthWechatMobile(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthWechat',
            'pageTitle' => '微信授权登录',
            'fields' => [

                'oauthWechatMobileEnable' => ['type' => FieldSwitch::class, 'title' => '[手机] 开启微信授权登录', 'desc' => ''],
                'oauthWechatMobileProxy' => ['type' => FieldText::class, 'title' => '[手机] 授权回调域名代理', 'desc' => '如不清楚此参数意义,请留空'],
                'oauthWechatMobileAppId' => ['type' => FieldText::class, 'title' => '[手机] AppId', 'desc' => ''],
                'oauthWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '[手机] AppSecret', 'desc' => ''],

            ]
        ]);
    }

    public function share(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'share',
            'pageTitle' => '分享设置',
            'fields' => [

                'shareWechatMobileEnable' => ['type' => FieldSwitch::class, 'title' => '微信分享开启', 'desc' => ''],
                'shareWechatMobileAppId' => ['type' => FieldText::class, 'title' => '微信分享AppId', 'desc' => ''],
                'shareWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '[手机] 微信分享AppSecret', 'desc' => ''],
                'shareWechatMobileImage' => ['type' => FieldImage::class, 'title' => '微信分享图标', 'desc' => ''],

            ]
        ]);
    }

    public function wxapp(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'wxapp',
            'pageTitle' => '微信小程序',
            'fields' => [

                'wxappEnable' => ['type' => FieldSwitch::class, 'title' => '微信小程序开启', 'desc' => ''],
                'wxappAppId' => ['type' => FieldText::class, 'title' => '微信小程序AppId', 'desc' => ''],
                'wxappAppSecret' => ['type' => FieldText::class, 'title' => '微信小程序AppSecret', 'desc' => ''],

            ]
        ]);
    }

    public function oauthQQ(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthQQ',
            'pageTitle' => 'QQ授权登录',
            'fields' => [
                'oauthQQEnable' => ['type' => FieldSwitch::class, 'title' => '开启QQ授权登录', 'desc' => '回调地址请填写 <code>' . RequestHelper::schema() . '://' . Request::server('HTTP_HOST') . '/oauth_callback_qq</code>'],
                'oauthQQKey' => ['type' => FieldText::class, 'title' => 'APP ID', 'desc' => ''],
                'oauthQQAppSecret' => ['type' => FieldText::class, 'title' => 'APP KEY', 'desc' => ''],
            ]
        ]);
    }

    public function oauthWeibo(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthWeibo',
            'pageTitle' => '微博授权登录',
            'fields' => [
                'oauthWeiboEnable' => ['type' => FieldSwitch::class, 'title' => '开启微博授权登录', 'desc' => '回调地址请填写 <code>' . RequestHelper::schema() . '://' . Request::server('HTTP_HOST') . '/oauth_callback_weibo</code>'],
                'oauthWeiboKey' => ['type' => FieldText::class, 'title' => 'Key', 'desc' => ''],
                'oauthWeiboAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }

    public function oauthWechatMiniProgram(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'wechatMiniProgram',
            'pageTitle' => '微信小程序',
            'fields' => [
                'oauthWechatMiniProgramEnable' => ['type' => FieldSwitch::class, 'title' => '启用小程序', 'desc' => ''],
                'oauthWechatMiniProgramAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'oauthWechatMiniProgramAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }

    public function retrieve(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'retrieve',
            'pageTitle' => '找回密码',
            'fields' => [
                'retrieveDisable' => ['type' => FieldSwitch::class, 'title' => '禁用找回密码', 'desc' => ''],
                'retrieveEmailEnable' => ['type' => FieldSwitch::class, 'title' => '开启邮件找回密码', 'desc' => '需要开启邮件发送'],
                'retrievePhoneEnable' => ['type' => FieldSwitch::class, 'title' => '开启手机短信找回密码', 'desc' => '需要开启短信发送'],
            ]
        ]);
    }


    public function input(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'retrieve',
            'pageTitle' => '输入设置',
            'fields' => [
                'editorType' => ['type' => FieldSelect::class, 'title' => '编辑器设置', 'desc' => '', 'options' => [
                    HtmlType::RICH_TEXT => '富文本',
                    HtmlType::MARKDOWN => 'Markdown',
                ],],
            ]
        ]);
    }

    public function ssoServer(ConfigCms $configCms)
    {
        if (RequestHelper::isPost()) {
            if (ConfigFacade::get('ssoClientEnable', false) && Input::get('ssoServerEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoServer',
            'pageTitle' => '同步登录服务端',
            'fields' => [
                'ssoServerEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录服务端', 'desc' => '本服务端地址为 <code>' . RequestHelper::domainUrl() . '/sso/server</code>'],
                'ssoServerSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录客户端通讯秘钥相同。<a href="javascript:;" onclick="$(\'[name=ssoServerSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'ssoClientList' => ['type' => FieldTextarea::class, 'title' => '允许的同步登录客户端列表', 'desc' => '每行一个 如 http://www.client.com/sso/client'],
            ]
        ]);
    }

    public function ssoClient(ConfigCms $configCms)
    {
        if (RequestHelper::isPost()) {
            if (ConfigFacade::get('ssoServerEnable', false) && Input::get('ssoClientEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoServer',
            'pageTitle' => '同步登录客户端',
            'fields' => [
                'ssoClientEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录客户端', 'desc' => '本客户端地址为 <code>' . RequestHelper::domainUrl() . '/sso/client</code>'],
                'ssoClientSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录服务端通讯秘钥相同。 <a href="javascript:;" onclick="$(\'[name=ssoClientSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'ssoServer' => ['type' => FieldText::class, 'title' => '同步登录服务端', 'desc' => '每行一个 如 http://www.server.com/sso/server'],
            ]
        ]);
    }

    public function upload(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'upload',
            'pageTitle' => '上传设置',
            'fields' => [

                'uploadDriver' => ['type' => FieldSelect::class, 'title' => '上传设置', 'desc' => '', 'options' => [
                    'local' => '本地存储',
                    'ossAliyun' => '阿里云存储',
                ],],
                'uploadDriverDomain' => ['type' => FieldText::class, 'title' => '阿里云OSS域名', 'desc' => '如 http://data.tecmz.com',],

                'uploadDriverAliyunAccessKeyId' => ['type' => FieldText::class, 'title' => '阿里云OSS AccessKeyId', 'desc' => '',],
                'uploadDriverAliyunAccessKeySecret' => ['type' => FieldText::class, 'title' => '阿里云OSS AccessKeySecret', 'desc' => '',],
                'uploadDriverAliyunEndpoint' => ['type' => FieldText::class, 'title' => '阿里云OSS Endpoint', 'desc' => '',],
                'uploadDriverAliyunBucket' => ['type' => FieldText::class, 'title' => '阿里云OSS Bucket', 'desc' => '',],

                'memberUploadWatermark' => ['type' => FieldSelect::class, 'title' => '用户上传水印', 'desc' => '', 'optionType' => WatermarkType::class,],
                'memberUploadWatermarkText' => ['type' => FieldText::class, 'title' => '用户水印文字', 'desc' => '',],
                'memberUploadWatermarkImage' => ['type' => FieldImage::class, 'title' => '用户水印图片', 'desc' => '',],
            ]
        ]);
    }


    public function apiAudioConvert(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'apiAudioConvert',
            'pageTitle' => '语音转换',
            'fields' => [
                'apiAudioConvertAppId' => ['type' => FieldText::class, 'title' => '语音转换AppId', 'desc' => '请在 <a href="http://api.tecmz.com" target="_blank">墨子API</a> 申请'],
                'apiAudioConvertAppSecret' => ['type' => FieldText::class, 'title' => '语音转换AppSecret', 'desc' => ''],
            ]
        ]);
    }

    public function apiAsr(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'apiAsr',
            'pageTitle' => '语音识别',
            'fields' => [
                'apiAsrAppId' => ['type' => FieldText::class, 'title' => '语音识别AppId', 'desc' => '请在 <a href="http://api.tecmz.com" target="_blank">墨子API</a> 申请'],
                'apiAsrAppSecret' => ['type' => FieldText::class, 'title' => '语音识别AppSecret', 'desc' => ''],
            ]
        ]);
    }

}