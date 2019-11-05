@extends('theme.default.pc.frame')

@section('pageTitleMain','修改密码')

@section('bodyContent')

    <div class="main-container">


        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li class="uk-active"><span>修改密码</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-6">
                @include('theme.default.pc.member.profile.menu')
            </div>
            <div class="uk-width-5-6">
                <div class="pb ">
                    <div class="head">修改密码</div>
                    <div class="content">
                        <form action="?" class="uk-form" method="post" data-ajax-form>
                            <div class="line">
                                <div class="label">旧密码:</div>
                                <div class="field">
                                    <input type="password" name="passwordOld" class="uk-width-2-5" />
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">新密码:</div>
                                <div class="field">
                                    <input type="password" name="passwordNew" class="uk-width-2-5" />
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">重复新密码:</div>
                                <div class="field">
                                    <input type="password" name="passwordRepeat" class="uk-width-2-5" />
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">&nbsp;</div>
                                <div class="field">
                                    <button type="submit" class="uk-button uk-button-primary">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection