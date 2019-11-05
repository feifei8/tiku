@if(\Edwin404\Tecmz\Helpers\OauthHelper::hasOauth())
    <div class="pb-oauth">
        <div class="title">
            您还可以使用以下方式登录
        </div>
        <div class="body">
            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isWechatMobileEnable())
                <a class="wechat" href="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::WECHAT_MOBILE}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-weixin"></i></a>
            @endif
            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isQQEnable())
                <a class="qq"
                   href="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::QQ}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-qq"></i></a>
            @endif
            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isWeiboEnable())
                <a class="weibo"
                   href="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::WEIBO}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-weibo"></i></a>
            @endif
        </div>
    </div>
@endif