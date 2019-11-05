@extends($_frameLayoutView)

@section('pageTitle','我的')
@section('pageTitleMain','我的')
@section('pageKeywords',htmlspecialchars(\Edwin404\Config\Facades\ConfigFacade::get('siteKeywords')))
@section('pageDescription',htmlspecialchars(\Edwin404\Config\Facades\ConfigFacade::get('siteDescription')))
@section('headerLeft')@endsection

@section('bodyContent')

    <div class="pb-member-home-head">
        <div class="body">
            @if(empty($_memberUser['id']))
                <a class="avatar" href="/login?redirect={{urlencode('/member')}}">
                    <img src="@assets('assets/lib/img/avatar.png')" />
                </a>
            @else
                <a class="avatar" href="/member/profile_avatar">
                    <img src="{{$_memberUser['avatar']}}"/>
                </a>
            @endif
            <div class="name">
                @if(empty($_memberUser['id']))
                    @if(!\Edwin404\Config\Facades\ConfigFacade::get('registerDisable',false))
                        登录 / 注册
                    @else
                        点击登录
                    @endif
                @else
                    {{$_memberUser['username']}}的中心
                @endif
            </div>
        </div>
    </div>

    <div class="pb-member-home-nav">

        <ul class="mui-table-view mui-table-view-chevron" style="margin-top:10px;">
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="/member/favorite_question">
                    <span class="iconfont">&#xe673;</span>
                    收藏的题目
                </a>
            </li>
        </ul>

        <ul class="mui-table-view mui-table-view-chevron" style="margin-top:10px;">
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="/member/profile">
                    <span class="iconfont">&#xe60b;</span>
                    我的资料
                </a>
            </li>
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="/member/profile_password">
                    <span class="iconfont">&#xe60e;</span>
                    修改密码
                </a>
            </li>
        </ul>

        @if($_memberUserId)
        <ul class="mui-table-view mui-table-view-chevron" style="margin-top:10px;">
            <li class="mui-table-view-cell">
                <a class="mui-navigate-right" href="javascript:;" data-confirm="确定退出？" data-href="/logout">
                    <span class="iconfont">&#xe70f;</span>
                    退出登录
                </a>
            </li>
        </ul>
        @endif

    </div>

@endsection