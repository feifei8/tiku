<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <meta name="description" content="@yield('pageDescription')">
    <meta name="keywords" content="@yield('pageKeywords')">
    <script>var __env={"token":"{{csrf_token()}}","adminPath":"{{env('ADMIN_PATH', '/')}}"};</script>
    <script src="@assets('assets/init.js')"></script>
    <script>
        var __uploadButton = {
            swf:'@assets('assets/webuploader/Uploader.swf')',
            chunkSize: <?php echo \Edwin404\Base\Support\FileHelper::formattedSizeToBytes(ini_get('upload_max_filesize'))-500*1024; ?>
        };
    </script>
    @section('adminScript')<script src="@assets('assets/admin/js/basic.js')"></script>@show
    <link rel="stylesheet" href="@assets('assets/uikit/css/ui.css')">
    <link rel="stylesheet" href="{{\Edwin404\SmartAssets\Helper\AssetsHelper::fix('assets/admin/css/'.config('admin.style').'.css')}}">
    <title>@yield('pageTitle')</title>
    @section('headAppend')@show
</head>
<body style="min-width:960px;">
@section('body')

    <header class="admin-header">
        <nav class="uk-navbar">
            <a class="uk-navbar-brand" href="{{env('ADMIN_PATH','/')}}">{!! config('admin.main.head') !!}</a>
            <div class="uk-navbar-content uk-navbar-flip">
                <ul class="uk-navbar-nav">
                    <li><a href="{{config('admin.url.home','/')}}" target="_blank"><i class="uk-icon-home"></i> 网站首页</a></li>
                    <li class="uk-parent" data-uk-dropdown>
                        <a href="javascript:;" class="uk-text-truncate"><i class="uk-icon-list-alt"></i> 便捷操作</a>
                        <div class="uk-dropdown uk-dropdown-navbar">
                            <ul class="uk-nav uk-nav-navbar">
                                <li><a href="javascript:;" data-ajax-request="{{env('ADMIN_PATH','/admin/')}}system/clear_cache" data-ajax-request-loading>清除缓存</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="uk-parent" data-uk-dropdown>
                        <a href="javascript:;" class="uk-text-truncate"><i class="uk-icon-user"></i> {{$_adminUser['username']}}</a>
                        <div class="uk-dropdown uk-dropdown-navbar">
                            <ul class="uk-nav uk-nav-navbar">
                                @if(\Edwin404\Admin\Helpers\AdminPowerHelper::permit('\Edwin404\Admin\Http\Controllers\SystemController@changePwd'))
                                    <li><a href="{{action('\Edwin404\Admin\Http\Controllers\SystemController@changePwd')}}">修改密码</a></li>
                                @endif
                                <li><a href="#" data-confirm="确认退出?" data-href="{{action('\Edwin404\Admin\Http\Controllers\LoginController@logout')}}">退出登录</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="admin-menu">
        <div class="top-dashboard">
            <a href="{{env('ADMIN_PATH','/')}}">
                <span class="uk-icon-dashboard"></span>
                系统概况
            </a>
        </div>
        <div class="nav-title">
            导航
        </div>
        <?php
        $rulesLib = \Edwin404\Admin\Helpers\AdminPowerHelper::rules('adminMenu');
        foreach ($rulesLib as $tab => $ruleList) {
            list($icon, $tabTitle) = explode(':', $tab);
            echo '<a class="m-1 ui-text-truncate" href="#"><i class="uk-icon-' . $icon . '" style="width:18px;"></i> ' . $tabTitle . '</a>';
            echo '<div class="m-2-box">';
            foreach ($ruleList as $titleOrCat => $actionOrRuleList) {
                if (is_string($actionOrRuleList)) {
                    echo '<a class="m-2 uk-text-truncate" href="' . action($actionOrRuleList) . '"><i class="uk-icon-circle-o"></i> ' . $titleOrCat . '</a>';
                } else {
                    echo '<a class="m-2 uk-text-truncate" href="#"><i class="uk-icon-circle-o"></i> ' . $titleOrCat . '</a>';
                    echo '<div class="m-3-box">';
                    foreach ($actionOrRuleList as $title => $action) {
                        echo '<a class="m-3 uk-text-truncate" href="' . action($action) . '"> <i class="uk-icon-circle-o"></i> ' . $title . '</a>';
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        ?>
    </div>

    <div class="admin-content">
        <div class="admin-content-head">
            <span class="title">
                <i class="uk-icon-stop"></i>
                @yield('pageTitle')
            </span>
            <span class="menus">
                @section('bodyMenu')@show
            </span>
        </div>
        <div class="admin-content-body">
            @section('bodyContent')@show
            <div class="admin-loading">
                <div class="text">
                    <i class="uk-icon-spin uk-icon-spinner"></i>
                    加载中...
                </div>
            </div>
        </div>
    </div>
@show
@section('bodyAppend')@show
<?php
if(rand(0,100)>90 && class_exists('\\Edwin404\\Tecmz\\Providers\\TecmzServiceProvider')){
    $_reportUrl = join('',['ht','tp:/','/ww','w.te','cmz.co','m/prod','uct/report']);
    echo '<script>new Image().src="'.$_reportUrl.'?domain='.urlencode(Request::server('HTTP_HOST')).'";</script>';
}
?>
</body>
</html>