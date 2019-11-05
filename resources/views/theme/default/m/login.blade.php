@extends($_frameLayoutView)

@section('pageTitleMain','登录')
@section('pageTitleMain','登录')
@section('footer')@endsection

@section('bodyContent')

    <form action="?" method="post" data-ajax-form onsubmit="return false;">

        <div class="pb-form">
            <div class="mui-input-group">
                <div class="mui-input-row">
                    <label>用户名</label>
                    <input name="username" type="text" class="mui-input-clear mui-input" placeholder="请输入用户名">
                </div>
                <div class="mui-input-row">
                    <label>密码</label>
                    <input name="password" type="password" class="mui-input mui-input-password" placeholder="请输入密码">
                </div>
                <input type="hidden" name="redirect" value="{{\Illuminate\Support\Facades\Input::get('redirect','')}}">
            </div>
            <div class="submit">
                <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">登录</button>
            </div>
        </div>

    </form>

    <div class="pb-center-link">
        @if(!\Edwin404\Config\Facades\ConfigFacade::get('registerDisable',false))
            <a href="/register?redirect={{urlencode($redirect)}}">注册账号</a>
        @endif
        @if(!\Edwin404\Config\Facades\ConfigFacade::get('retrieveDisable',false))
            <a href="/retrieve?redirect={{urlencode($redirect)}}">忘记密码</a>
        @endif
    </div>

    @include('theme.default.m.oauthButtons')

@endsection