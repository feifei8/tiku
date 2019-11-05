@extends('theme.default.pc.frame')

@section('pageTitleMain','设置用户名')



@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/register">注册</a></li>
                <li class="uk-active"><span>设置用户名</span></li>
            </ul>
        </div>

        <div class="pb pb-account">
            <div class="head">
                <h1>设置用户名</h1>
            </div>
            <div class="body">
                <div class="uk-grid">
                    <div class="uk-width-1-2">
                        <div class="form">
                            <form action="?" method="post" class="uk-form" data-ajax-form>
                                <div class="line">
                                    <div class="label">用户名：</div>
                                    <div class="field">
                                        <input type="text" name="username" />
                                        <div class="help">
                                            为方便后期登录使用，请您设置一个用户名
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