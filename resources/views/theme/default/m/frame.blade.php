<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fixOrDefault(\Edwin404\Config\Facades\ConfigFacade::get('siteFavIco'),'default_favicon.ico')}}" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="keywords" content="@yield('pageKeywords',\Edwin404\Config\Facades\ConfigFacade::get('siteKeywords'))">
    <meta name="description" content="@yield('pageDescription',\Edwin404\Config\Facades\ConfigFacade::get('siteDescription'))">
    <title>@section('pageTitle')@yield('pageTitleMain') · {{\Edwin404\Config\Facades\ConfigFacade::get('siteName')}}@show</title>
    <link href="@assets('assets/mui/css/mui.css')" rel="stylesheet"/>
    <link rel="stylesheet" href="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix('theme/'.\Edwin404\Config\Facades\ConfigFacade::get('siteTemplate','default').'/m/css/style.css')}}"/>
    <script src="@assets('assets/init.js')"></script>
    @if(\Edwin404\Common\Helpers\AgentHelper::isWechat())
        <script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    @endif
    @section('headAppend')@show
    {!! \Edwin404\Config\Facades\ConfigFacade::get('systemCounter') !!}
</head>
<body>
@section('body')

@section('header')
    @if(\Edwin404\Tecmz\Helpers\UserAgentHelper::source()!=\Edwin404\Tecmz\Helpers\UserAgentHelper::SOURCE_APP && !\Edwin404\Common\Helpers\AgentHelper::isWechat())
        <header class="mui-bar mui-bar-nav">
            @section('headerLeft')
                <a class="mui-action-back mui-pull-left"><i class="mui-icon mui-icon-left-nav"></i></a>
            @show
            <h1 class="mui-title">
                @yield('pageTitleMain','<img src="'.\Edwin404\SmartAssets\Helper\AssetsHelper::fixOrDefault(\Edwin404\Config\Facades\ConfigFacade::get('siteLogo'),'/placeholder/160x50').'" class="logo" />')
            </h1>
            @section('headerRight')
                {{--<a class="mui-icon mui-icon-search mui-pull-right" href="javascript:;" onclick="$('.pb-search-bar').show();$('body,html').animate({scrollTop:0});"></a>--}}
            @show
        </header>
    @endif
@show

<div class="mui-content">

    @section('bodyContent')
    @show

    @section('footer')

        <div style="height:60px;"></div>

        <nav class="mui-bar mui-bar-tab">
            <a class="mui-tab-item @if($request_path=='/') mui-active @endif" href="/">
                <span class="mui-icon iconfont">&#xe9bb;</span>
                <span class="mui-tab-label">首页</span>
            </a>
            <a class="mui-tab-item @if(\Illuminate\Support\Str::startsWith($request_path,'/question')) mui-active @endif" href="/question">
                <span class="mui-icon iconfont">&#xe650;</span>
                <span class="mui-tab-label">题目</span>
            </a>
            @if(0)
            <a class="mui-tab-item @if(\Illuminate\Support\Str::startsWith($request_path,'/tags')) mui-active @endif" href="/cart">
                <span class="mui-icon iconfont">&#xe650;</span>
                <span class="mui-tab-label">专项</span>
            </a>
            <a class="mui-tab-item @if(\Illuminate\Support\Str::startsWith($request_path,'/paper')) mui-active @endif" href="/cart">
                <span class="mui-icon iconfont">&#xe650;</span>
                <span class="mui-tab-label">试卷</span>
            </a>
            @endif
            <a class="mui-tab-item @if(\Illuminate\Support\Str::startsWith($request_path,'/member')) mui-active @endif" href="/member">
                <span class="mui-icon iconfont">&#xe68c;</span>
                <span class="mui-tab-label">我的</span>
            </a>
        </nav>

    @show

</div>
@section('bodyScript')
    <script src="@assets('assets/m/default/basic.js')"></script>
@show
@section('bodyWechatScript')
    @if(\Edwin404\Common\Helpers\AgentHelper::isWechat() && \Edwin404\Config\Facades\ConfigFacade::get('shareWechatMobileEnable',false))
        <script>
            wx.config(<?php echo \Edwin404\Tecmz\Helpers\WechatHelper::shareApp()->js->config(['onMenuShareTimeline', 'onMenuShareAppMessage'],false); ?>);
            wx.ready(function () {
                if(typeof __WxShare=='undefined'){
                    window.__WxShare = {};
                    window.__WxShare.title = window.document.title;
                    window.__WxShare.desc = $('meta[name=description]').attr('content');
                    window.__WxShare.link = window.location.href;
                    window.__WxShare.imgUrl = <?php echo json_encode(\Edwin404\SmartAssets\Helper\AssetsHelper::fixFull(\Edwin404\Config\Facades\ConfigFacade::get('shareWechatMobileImage'))); ?>;
                }
                wx.onMenuShareAppMessage({
                    title: __WxShare.title,
                    desc:__WxShare.desc,
                    link: __WxShare.link,
                    imgUrl: __WxShare.imgUrl,
                    type: 'link',
                    dataUrl: '',
                    success: function () {},
                    cancel: function () {}
                });
                wx.onMenuShareTimeline({
                    title: __WxShare.title,
                    desc:__WxShare.desc,
                    link: __WxShare.link,
                    imgUrl: __WxShare.imgUrl,
                    success: function () {},
                    cancel: function () {}
                });
            });
        </script>
    @endif
@show
@section('bodyAppend')@show
@show
</body>
</html>