@extends($_frameLayoutView)

@section('pageTitleMain','通过邮箱找回密码')
@section('footer')@endsection

@section('bodyScript')
    @parent
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '/retrieve/email_verify',
                selectorTarget: 'input[name=email]',
                selectorGenerate: '[data-verify-generate]',
                selectorCountdown: '[data-verify-countdown]',
                selectorRegenerate: '[data-verify-regenerate]',
                selectorCaptcha: 'input[name=captcha]',
                selectorCaptchaImg:'img[data-captcha]',
                interval: 60,
            },window.api.dialog);
        });
    </script>
@endsection

@section('bodyContent')

    <div class="pb-form">
        <form action="?" method="post" data-ajax-form onsubmit="return false;">
            <div class="mui-input-group">
                <div class="mui-input-row">
                    <label>邮箱</label>
                    <input name="email" type="text" class="mui-input-clear mui-input" placeholder="请输入邮箱">
                </div>
                <div class="mui-input-row captcha">
                    <img data-captcha src="/retrieve/captcha" onclick="this.src='/retrieve/captcha?'+Math.random();"/>
                    <label>图形验证码</label>
                    <input type="text" name="captcha" class="mui-input-clear mui-input" placeholder="输入验证码" />
                </div>
                <div class="mui-input-row captcha">
                    <div class="btn">
                        <button type="button" data-verify-generate>获取验证码</button>
                        <button type="button" data-verify-countdown style="display:none;"></button>
                        <button type="button" data-verify-regenerate style="display:none;">重新获取</button>
                    </div>
                    <label>短信验证码</label>
                    <input type="text" name="verify" class="mui-input-clear mui-input" placeholder="输入验证码" />
                </div>
            </div>

            <div class="submit">
                <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">提交</button>
            </div>

        </form>
    </div>

@endsection