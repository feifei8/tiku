@extends('theme.default.pc.frame')

@section('pageTitleMain','用户登录')

@section('bodyContent')


<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>用户登录</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>用户登录</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="form">
                        <form action="?" method="post" class="uk-form" data-ajax-form>
                            <div class="line">
                                <div class="label">用户：</div>
                                <div class="field">
                                    <input type="text" name="username" />
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">密码：</div>
                                <div class="field">
                                    <input type="password" name="password" />
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="field">
                                    <button type="submit" class="uk-button uk-button-primary">提交</button>
                                </div>
                            </div>
                            <input type="hidden" name="redirect" value="{{$redirect}}" />
                        </form>
                    </div>
                </div>
                <div class="uk-width-1-2">
                    <div class="text">
                        <div>
                            没有账号？<a href="javascript:;" data-dialog-request="/register?redirect={{urlencode($redirect)}}">马上注册</a>
                        </div>
                        <div>
                            忘记密码？<a href="/retrieve?redirect={{urlencode($redirect)}}">找回密码</a>
                        </div>
                        @if(\Edwin404\Tecmz\Helpers\OauthHelper::hasOauth())
                        <div>
                            您还可以使用以下方式登录
                        </div>
                        <div class="oauth">
                            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isWechatEnable())
                            <a class="item wechat" href="javascript:;" data-dialog-request="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::WECHAT}}?redirect={{urlencode($redirect)}}"><i class="uk-icon-wechat"></i></a>
                            @endif
                            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isQQEnable())
                            <a class="item qq" href="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::QQ}}?redirect={{urlencode($redirect)}}"><i class="uk-icon-qq"></i></a>
                            @endif
                            @if(\Edwin404\Tecmz\Helpers\OauthHelper::isWeiboEnable())
                            <a class="item weibo" href="/oauth_login_{{\Edwin404\Oauth\Types\OauthType::WEIBO}}?redirect={{urlencode($redirect)}}"><i class="uk-icon-weibo"></i></a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection