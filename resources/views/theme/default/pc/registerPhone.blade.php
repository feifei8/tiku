@extends('theme.default.pc.frame')

@section('pageTitleMain','用户注册')


@section('bodyScript')
    <script src="@assets('assets/main/default/verify.js')"></script>
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '/register/phone_verify',
                selectorTarget: 'input[name=phone]',
                selectorGenerate: '[data-phone-verify-generate]',
                selectorCountdown: '[data-phone-verify-countdown]',
                selectorRegenerate: '[data-phone-verify-regenerate]',
                selectorCaptcha: 'input[name=phoneCaptcha]',
                selectorCaptchaImg:'img[data-phone-captcha]',
                interval: 60,
            },window.api.dialog);
        });
    </script>
@endsection

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li class="uk-active"><span>用户注册</span></li>
            </ul>
        </div>

        <div class="pb pb-account">
            <div class="head">
                <h1>用户注册</h1>
            </div>
            <div class="body">
                <div class="uk-grid">
                    <div class="uk-width-1-2">
                        <div class="form">
                            <form action="?" method="post" class="uk-form" data-ajax-form>
                                <div class="line">
                                    <div class="label">手机：</div>
                                    <div class="field">
                                        <input type="text" name="phone" class="uk-width-3-5" value="" />
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">图片验证：</div>
                                    <div class="field">
                                        <input type="text" name="phoneCaptcha" class="uk-width-1-5" value="" />
                                        <img data-phone-captcha src="/register/captcha" style="height:30px;border:1px solid #CCC;border-radius:3px;" alt="刷新验证码" onclick="this.src='/register/captcha?'+Math.random();"/>
                                        <button class="uk-button uk-button-default" type="button" data-phone-verify-generate>获取验证码</button>
                                        <button class="uk-button uk-button-default uk-disabled" type="button" data-phone-verify-countdown style="display:none;"></button>
                                        <button class="uk-button uk-button-default" type="button" data-phone-verify-regenerate style="display:none;">重新获取验证码</button>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">手机验证：</div>
                                    <div class="field">
                                        <input type="text" name="phoneVerify" value="" placeholder="" class="uk-width-1-5" />
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">登录密码：</div>
                                    <div class="field">
                                        <input type="password" name="password" class="uk-width-3-5" />
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
                                已有账号？<a href="/login?redirect={{urlencode($redirect)}}">马上登录</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection