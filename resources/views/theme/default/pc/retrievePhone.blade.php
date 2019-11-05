@extends('theme.default.pc.frame')

@section('pageTitleMain','通过手机找回密码')

@section('bodyScript')
    <script src="@assets('assets/main/default/verify.js')"></script>
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '/retrieve/phone_verify',
                selectorTarget: 'input[name=phone]',
                selectorGenerate: '[data-phone-verify-generate]',
                selectorCountdown: '[data-phone-verify-countdown]',
                selectorRegenerate: '[data-phone-verify-regenerate]',
                selectorCaptcha: 'input[name=captcha]',
                selectorCaptchaImg:'img[data-captcha]',
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
                <li class="uk-active"><span>通过手机找回密码</span></li>
            </ul>
        </div>

        <div class="pb pb-account">
            <div class="head">
                <h1>通过手机找回密码</h1>
            </div>
            <div class="body">
                <div class="uk-grid">
                    <div class="uk-width-1-2">
                        <div class="form">

                            <form action="?" method="post" class="uk-form" data-ajax-form>
                                <div class="line">
                                    <div class="label">手机：</div>
                                    <div class="field">
                                        <input type="text" name="phone" />
                                        <div class="help">
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">图形验证码：</div>
                                    <div class="field">
                                        <div class="uk-grid">
                                            <div class="uk-width-1-3">
                                                <img data-captcha src="/retrieve/captcha" style="height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='/retrieve/captcha?'+Math.random();"/>
                                            </div>
                                            <div class="uk-width-1-3">
                                                <input type="text" name="captcha" class="uk-width-1-1" />
                                            </div>
                                            <div class="uk-width-1-3">
                                                <button class="uk-button uk-button-default" type="button" data-phone-verify-generate>获取验证码</button>
                                                <button class="uk-button uk-button-default uk-disabled" type="button" data-phone-verify-countdown style="display:none;"></button>
                                                <button class="uk-button uk-button-default" type="button" data-phone-verify-regenerate style="display:none;">重新获取</button>
                                            </div>
                                        </div>
                                        <div class="help">
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">手机验证码：</div>
                                    <div class="field">
                                        <input type="text" name="verify" />
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
                                还没有账号？<a href="/register?redirect={{urlencode($redirect)}}">马上注册</a>
                            </div>
                            <div>
                                已想起来密码？<a href="/login?redirect={{urlencode($redirect)}}">马上登录</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection