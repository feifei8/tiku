@extends('admin::frame')

@section('pageTitle','后台首页')

@section('adminScript')
    @parent
    <script>var __version_check_url = '{{\App\Constant\AppConstant::VERSION_CHECK_URL}}';</script>
    <script src="@assets('assets/admin/js/versionCheck.js')"></script>
@endsection

@section('bodyContent')

    @if (env('ADMIN_DEMO_USER_ID', 0) && $_adminUserId == env('ADMIN_DEMO_USER_ID', 0))
        <div class="uk-alert uk-alert-danger">
            当前账号为 <strong>演示账号</strong>，不能进行 增加/编辑/删除 操作。
        </div>
    @endif

    <div class="uk-grid">
        <div class="uk-width-1-2">
            <div class="admin-block-stat">
                <div class="icon green">
                    <i class="uk-icon-users"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\MemberController@dataList')}}">
                    {{number_format(\Edwin404\Base\Support\ModelHelper::count('member_user'))}}
                </a>
                <div class="name">
                    会员总数
                </div>
            </div>
        </div>
        <div class="uk-width-1-2">
            <div class="admin-block-stat">
                <div class="icon gavel">
                    <i class="uk-icon-users"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\PaperExamController@dataList')}}">
                    {{number_format(\Edwin404\Base\Support\ModelHelper::count('paper_exam'))}}
                </a>
                <div class="name">
                    考试人次
                </div>
            </div>
        </div>
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon red">
                    <i class="uk-icon-th"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\QuestionController@dataList')}}">
                    {{number_format(\Edwin404\Base\Support\ModelHelper::count('question'))}}
                </a>
                <div class="name">
                    题目总数
                </div>
            </div>
        </div>
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon blue">
                    <i class="uk-icon-list"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\NewsController@dataList')}}">
                    {{number_format(\Edwin404\Base\Support\ModelHelper::count('news'))}}
                </a>
                <div class="name">
                    资讯总数
                </div>
            </div>
        </div>
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon dark">
                    <i class="uk-icon-file"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\PaperController@dataList')}}">
                    {{number_format(\Edwin404\Base\Support\ModelHelper::count('paper'))}}
                </a>
                <div class="name">
                    试卷总数
                </div>
            </div>
        </div>
    </div>

    <div class="uk-grid">
        <div class="uk-width-1-2">
            <div class="admin-block">
                <div class="head"><i class="uk-icon-cog"></i> 系统概况</div>
                <div class="body" style="height:160px;">
                    <div>
                        版本：<span>V{{\App\Constant\AppConstant::VERSION}}</span>
                    </div>
                    <div style="padding:10px 0 0 0;" data-admin-version="{{\App\Constant\AppConstant::VERSION}}"></div>
                </div>
            </div>
        </div>
        <div class="uk-width-1-2">
            <div class="admin-block">
                <div class="head"><i class="uk-icon-copyright"></i> 版权说明</div>
                <div class="body" style="height:160px;">
                    <table class="uk-table">
                        <tbody>
                        <tr>
                            <td>
                                使用中遇到问题请 <a href="javascript:;" data-dialog-title="问题反馈" data-dialog-request="http://www.tecmz.com/product/tiku/feedback?from=dialog&version={{\App\Constant\AppConstant::VERSION}}"><i class="uk-icon-user"></i> 反馈给我们</a>，如需系统定制请 <a href="http://www.tecmz.com/product/tiku" target="_blank"><i class="uk-icon-headphones"></i> 联系我们</a>。
                            </td>
                        </tr>
                        <tr>
                            <td data-admin-auth>
                                请您在使用过程中始终保留版权，如需商业授权请 <a href="http://www.tecmz.com/product/tiku" target="_blank"><i class="uk-icon-key"></i> 联系我们进行授权</a>。
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection