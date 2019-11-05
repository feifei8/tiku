<?php
return [

    'users:用户管理' => [
        '用户管理' => '\App\Http\Controllers\Admin\MemberController@dataList',
    ],
    'database:题库管理' => [
        '添加题目' => '\App\Http\Controllers\Admin\QuestionController@dataAdd',
        '题库题目' => '\App\Http\Controllers\Admin\QuestionController@dataList',
        '题库标签' => '\App\Http\Controllers\Admin\QuestionTagController@dataList',
        '题库标签组' => '\App\Http\Controllers\Admin\QuestionTagGroupController@dataList',
    ],

    'comment:题目评论' => [
        '评论管理' => '\App\Http\Controllers\Admin\QuestionCommentController@dataList',
    ],

    'gavel:试卷管理' => [
        '手动组卷' => '\App\Http\Controllers\Admin\PaperController@dataAdd',
        '试卷列表' => '\App\Http\Controllers\Admin\PaperController@dataList',
        '考试管理' => '\App\Http\Controllers\Admin\PaperExamController@dataList',
        '试卷分类' => '\App\Http\Controllers\Admin\PaperCategoryController@dataList',
    ],

    'list:资讯管理' => [
        '资讯分类' => '\App\Http\Controllers\Admin\NewsCategoryController@dataList',
        '资讯管理' => '\App\Http\Controllers\Admin\NewsController@dataList',
    ],

    'cog:基础设置' => [
        '基本设置' => '\App\Http\Controllers\Admin\ConfigController@setting',
        '访问设置' => '\App\Http\Controllers\Admin\ConfigController@visit',
        '轮播设置' => '\App\Http\Controllers\Admin\BannerController@dataList',
        '文章管理' => '\App\Http\Controllers\Admin\ArticleController@dataList',
        '合作伙伴' => '\App\Http\Controllers\Admin\PartnerController@dataList',
        '广告位' => '\App\Http\Controllers\Admin\AdController@dataList',
        '短信/邮件' => [
            '邮件发送' => '\App\Http\Controllers\Admin\ConfigController@email',
            '短信发送' => '\App\Http\Controllers\Admin\ConfigController@sms',
        ],
        '注册登录' => [
            '微信授权登录' => '\App\Http\Controllers\Admin\ConfigController@oauthWechat',
            'QQ授权登录' => '\App\Http\Controllers\Admin\ConfigController@oauthQQ',
            '微博授权登录' => '\App\Http\Controllers\Admin\ConfigController@oauthWeibo',
            '找回密码' => '\App\Http\Controllers\Admin\ConfigController@retrieve',
            '注册设置' => '\App\Http\Controllers\Admin\ConfigController@register',
            '同步登录服务端' => '\App\Http\Controllers\Admin\ConfigController@ssoServer',
            '同步登录客户端' => '\App\Http\Controllers\Admin\ConfigController@ssoClient',
        ],

        '自动运营' => [
            '自动发布资讯' => '\App\Http\Controllers\Admin\MarketingController@news',
        ],

    ],

    'cogs:系统管理' => [
        'HIDE:修改密码' => '\Edwin404\Admin\Http\Controllers\SystemController@changePwd',

        '管理员角色' => '\Edwin404\Admin\Http\Controllers\SystemController@userRoleList',
        'HIDE:角色修改' => '\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit',
        'HIDE:角色删除' => '\Edwin404\Admin\Http\Controllers\SystemController@userRoleDelete',

        '管理员' => '\Edwin404\Admin\Http\Controllers\SystemController@userList',
        'HIDE:管理员修改' => '\Edwin404\Admin\Http\Controllers\SystemController@userEdit',
        'HIDE:管理员删除' => '\Edwin404\Admin\Http\Controllers\SystemController@userDelete',

        '操作日志' => '\Edwin404\Admin\Http\Controllers\SystemController@logList',
        'HIDE:操作日志删除' => '\Edwin404\Admin\Http\Controllers\SystemController@logDelete',

    ],


];