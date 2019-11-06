<!doctype html>
<html class="no-js">

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fixOrDefault(\Edwin404\Config\Facades\ConfigFacade::get('siteFavIco'),'default_favicon.ico')}}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="@yield('pageKeywords',\Edwin404\Config\Facades\ConfigFacade::get('siteKeywords'))">
    <meta name="description" content="@yield('pageDescription',\Edwin404\Config\Facades\ConfigFacade::get('siteDescription'))">
    <meta name="viewport" content="width=device-width, minimum-scale=0.5, maximum-scale=5, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <script src="@assets('assets/init.js')"></script>
    <title>@section('pageTitle')@yield('pageTitleMain') - {{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}@show</title>
    @section('headScript')
    <link rel="stylesheet" href="@assets('assets/uikit/css/ui.css')" />
    <link rel="stylesheet" href="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix('theme/'.\Edwin404\Config\Facades\ConfigFacade::get('siteTemplate','default').'/pc/css/style.css')}}" />
    @show
    @section('headAppend')@show
    {!! \Edwin404\Config\Facades\ConfigFacade::get('systemCounter') !!}
</head>

<body>
    @section('body')

    <header>
        <div class="main-container">
            <nav class="uk-navbar">
                <ul class="uk-navbar-nav uk-navbar-nav-right">
                    @if(empty($_memberUser))
                    <!-- @if(!\Edwin404\Config\Facades\ConfigFacade::get('registerDisable',false))
                            <li><a href="/register">注册</a></li>
                        @endif -->
                    <li><a href="javascript:;" data-dialog-request="/login">微信登录</a></li>
                    @else
                    <li>
                        <a href="/member/message" class="notice">
                            <i class="uk-icon-bell"></i>
                            <?php $count = \Edwin404\Member\Facades\MemberMessageFacade::getMemberUnreadMessageCount($_memberUser['id']); ?>
                            @if($count)
                            <span class="count" data-member-unread-message-count>{{$count}}</span>
                            @endif
                        </a>
                    </li>
                    <li class="uk-parent" data-uk-dropdown>
                        <a href="javascript:;" class="username">{{\Edwin404\Member\Helpers\MemberHelper::name($_memberUser)}}</a>
                        <div class="uk-dropdown uk-dropdown-navbar uk-dropdown-bottom" style="top: 40px; left: 0px;">
                            <ul class="uk-nav uk-nav-navbar">
                                <li><a href="/member"><i class="uk-icon-user"></i> 个人中心</a></li>
                                <li><a data-confirm="确定退出?" data-href="/logout"><i class="uk-icon-sign-out"></i> 退出</a></li>
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>
                <a class="uk-navbar-brand" href="/"><img src="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fixOrDefault(\Edwin404\Config\Facades\ConfigFacade::get('siteLogo'),'/placeholder/160x50')}}" /></a>
                <ul class="uk-navbar-nav">
                    <li @if($request_path=='/' ) class="uk-active" @endif><a href="/">首页</a></li>
                    <li @if(\Illuminate\Support\Str::startsWith($request_path,'/question')) class="uk-active" @endif><a href="/question">题目</a></li>
                    <li @if(\Illuminate\Support\Str::startsWith($request_path,'/tags')) class="uk-active" @endif><a href="/tags">专项</a></li>
                    <li @if(\Illuminate\Support\Str::startsWith($request_path,'/paper')) class="uk-active" @endif><a href="/paper">试卷</a></li>
                    <li @if(\Illuminate\Support\Str::startsWith($request_path,'/news')) class="uk-active" @endif><a href="/news">资讯</a></li>
                </ul>
            </nav>
        </div>
    </header>

    @section('bodyContent')@show

    <footer>
        <div class="main-container">
            <div class="articles">
                @foreach($footerArticles as $footerArticle)
                <a href="/article/{{$footerArticle['id']}}">{{$footerArticle['title']}}</a>
                @endforeach
            </div>
            <div class="copyright">
                <a href="http://www.miitbeian.gov.cn" target="_blank">{{\Edwin404\Config\Facades\ConfigFacade::get('siteBeian','[网站备案信息]')}}</a>
                &copy;
                {{\Edwin404\Config\Facades\ConfigFacade::get('siteDomain')}}
            </div>
        </div>
    </footer>
    @show
    @section('bodyScript')
    <script src="@assets('assets/main/default/basic.js')"></script>
    @show
    @section('bodyAppend')@show
</body>

</html>