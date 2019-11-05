@extends('theme.default.pc.frame')

@section('pageTitleMain','我的资料')

@section('bodyContent')

    <div class="main-container">

        <div class="pb pb-breadcrumb">
            <ul class="uk-breadcrumb">
                <li><a href="/">首页</a></li>
                <li><a href="/member/{{$_memberUser['id']}}">我的中心</a></li>
                <li class="uk-active"><span>我的资料</span></li>
            </ul>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-6">
                @include('theme.default.pc.member.profile.menu')
            </div>
            <div class="uk-width-5-6">
                <div class="pb">
                    <div class="head">我的资料</div>
                    <div class="content">
                        <form action="?" class="uk-form" method="post" data-ajax-form>
                            <div class="line">
                                <div class="label">用户名:</div>
                                <div class="field">
                                    @if(empty($_memberUser['username']))
                                        <input type="text" name="username" />
                                        <div class="help">
                                            用户名设定后将不能修改
                                        </div>
                                    @else
                                        {{$_memberUser['username']}}
                                    @endif
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">性别:</div>
                                <div class="field">
                                    <label><input type="radio" name="gender" value="{{\Edwin404\Member\Types\Gender::MALE}}" @if($_memberUser['gender']==\Edwin404\Member\Types\Gender::MALE) checked @endif /> 男</label>
                                    &nbsp;
                                    <label><input type="radio" name="gender" value="{{\Edwin404\Member\Types\Gender::FEMALE}}" @if($_memberUser['gender']==\Edwin404\Member\Types\Gender::FEMALE) checked @endif /> 女</label>
                                    &nbsp;
                                    <label><input type="radio" name="gender" value="{{\Edwin404\Member\Types\Gender::UNKNOWN}}" @if($_memberUser['gender']==\Edwin404\Member\Types\Gender::UNKNOWN) checked @endif /> 保密</label>
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">真实姓名:</div>
                                <div class="field">
                                    <input type="text" name="realname" value="{{$_memberUser['realname'] or ''}}" placeholder="" class="uk-width-1-5" />
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">个性签名:</div>
                                <div class="field">
                                    <input type="text" name="signature" value="{{$_memberUser['signature'] or ''}}" placeholder="" class="uk-width-3-5" />
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