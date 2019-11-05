@extends($_frameLayoutView)

@section('pageTitleMain','用户注册')
@section('footer')@endsection

@section('bodyScript')
    @parent
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '/register/email_verify',
                selectorTarget: 'input[name=email]',
                selectorGenerate: '[data-email-verify-generate]',
                selectorCountdown: '[data-email-verify-countdown]',
                selectorRegenerate: '[data-email-verify-regenerate]',
                selectorCaptcha: 'input[name=emailCaptcha]',
                selectorCaptchaImg:'img[data-email-captcha]',
                interval: 60,
            },window.api.dialog);
        });
    </script>
@endsection

@section('bodyContent')

    <form action="?" method="post" data-ajax-form onsubmit="return false;">

        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>邮箱</label>
                <input name="email" type="text" class="mui-input-clear mui-input" placeholder="请输入邮箱">
            </div>
            <div class="mui-input-row captcha">
                <img data-email-captcha src="/register/captcha" onclick="this.src='/register/captcha?'+Math.random();"/>
                <label>图形验证码</label>
                <input type="text" name="emailCaptcha" class="mui-input-clear mui-input" placeholder="输入验证码" />
            </div>
            <div class="mui-input-row captcha">
                <div class="btn">
                    <button type="button" data-email-verify-generate>获取验证码</button>
                    <button type="button" data-email-verify-countdown style="display:none;"></button>
                    <button type="button" data-email-verify-regenerate style="display:none;">重新获取</button>
                </div>
                <label>短信验证码</label>
                <input type="text" name="emailVerify" class="mui-input-clear mui-input" placeholder="输入验证码" />
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