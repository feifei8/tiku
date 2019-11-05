@extends($_frameLayoutView)

@section('pageTitleMain','用户注册')
@section('footer')@endsection

@section('bodyScript')
    @parent
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

    <form action="?" method="post" data-ajax-form onsubmit="return false;">

        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>手机</label>
                <input name="phone" type="text" class="mui-input-clear mui-input" placeholder="请输入手机">
            </div>
            <div class="mui-input-row captcha">
                <img data-phone-captcha src="/register/captcha" onclick="this.src='/register/captcha?'+Math.random();"/>
                <label>图形验证码</label>
                <input type="text" name="phoneCaptcha" class="mui-input-clear mui-input" placeholder="输入验证码" />
            </div>
            <div class="mui-input-row captcha">
                <div class="btn">
                    <button type="button" data-phone-verify-generate>获取验证码</button>
                    <button type="button" data-phone-verify-countdown style="display:none;"></button>
                    <button type="button" data-phone-verify-regenerate style="display:none;">重新获取</button>
                </div>
                <label>短信验证码</label>
                <input type="text" name="phoneVerify" class="mui-input-clear mui-input" placeholder="输入验证码" />
            </div>
            <div class="mui-input-row">
                <label>登录密码</label>
                <input name="password" type="password" class="mui-input-password mui-input" placeholder="请输入密码">
            </div>
            <input type="hidden" name="redirect" value="{{\Illuminate\Support\Facades\Input::get('redirect','')}}">
        </div>

        <div class="submit">
            <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">注册</button>
        </div>

    </form>

    <div class="pb-center-link">
        <a href="/login?redirect={{urlencode($redirect)}}">立即登录</a>
        @if(!\Edwin404\Config\Facades\ConfigFacade::get('retrieveDisable',false))
            <a href="/retrieve?redirect={{urlencode($redirect)}}">忘记密码</a>
        @endif
    </div>

@endsection