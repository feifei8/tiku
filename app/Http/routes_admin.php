<?php

Route::group(
    [
        'prefix' => env('ADMIN_PATH', '/admin/'),
        'middleware' => [
            \Edwin404\Admin\Http\Middleware\AdminWebAuth::class,
        ]
    ], function () {

    // 公用
    include __DIR__ . '/../../vendor/edwin404/laravel-admin/src/Config/routes.php';
    Route::match(['get', 'post'], '', '\App\Http\Controllers\Admin\IndexController@index');
    Route::match(['get', 'post'], 'system/data/image_select_dialog', '\Edwin404\Admin\Http\Controllers\DataController@imageSelectDialog');
    Route::match(['get', 'post'], 'system/data/temp_data_upload/{category}', '\Edwin404\Admin\Http\Controllers\DataController@tempDataUpload');
    // 公用

    Route::match(['get', 'post'], 'member/list', '\App\Http\Controllers\Admin\MemberController@dataList');
    Route::match(['get', 'post'], 'member/view', '\App\Http\Controllers\Admin\MemberController@dataView');
    Route::match(['get', 'post'], 'member/edit', '\App\Http\Controllers\Admin\MemberController@dataEdit');
    Route::match(['get', 'post'], 'member/add', '\App\Http\Controllers\Admin\MemberController@dataAdd');
    Route::match(['get', 'post'], 'member/delete', '\App\Http\Controllers\Admin\MemberController@dataDelete');
    Route::match(['get', 'post'], 'member/export', '\App\Http\Controllers\Admin\MemberController@dataExport');

    Route::match(['get', 'post'], 'banner/list', '\App\Http\Controllers\Admin\BannerController@dataList');
    Route::match(['get', 'post'], 'banner/view', '\App\Http\Controllers\Admin\BannerController@dataView');
    Route::match(['get', 'post'], 'banner/edit', '\App\Http\Controllers\Admin\BannerController@dataEdit');
    Route::match(['get', 'post'], 'banner/add', '\App\Http\Controllers\Admin\BannerController@dataAdd');
    Route::match(['get', 'post'], 'banner/delete', '\App\Http\Controllers\Admin\BannerController@dataDelete');

    Route::match(['get', 'post'], 'ad/list', '\App\Http\Controllers\Admin\AdController@dataList');
    Route::match(['get', 'post'], 'ad/view', '\App\Http\Controllers\Admin\AdController@dataView');
    Route::match(['get', 'post'], 'ad/edit', '\App\Http\Controllers\Admin\AdController@dataEdit');
    Route::match(['get', 'post'], 'ad/add', '\App\Http\Controllers\Admin\AdController@dataAdd');
    Route::match(['get', 'post'], 'ad/delete', '\App\Http\Controllers\Admin\AdController@dataDelete');

    Route::match(['get', 'post'], 'partner/list', '\App\Http\Controllers\Admin\PartnerController@dataList');
    Route::match(['get', 'post'], 'partner/view', '\App\Http\Controllers\Admin\PartnerController@dataView');
    Route::match(['get', 'post'], 'partner/edit', '\App\Http\Controllers\Admin\PartnerController@dataEdit');
    Route::match(['get', 'post'], 'partner/add', '\App\Http\Controllers\Admin\PartnerController@dataAdd');
    Route::match(['get', 'post'], 'partner/delete', '\App\Http\Controllers\Admin\PartnerController@dataDelete');

    Route::match(['get', 'post'], 'article/list', '\App\Http\Controllers\Admin\ArticleController@dataList');
    Route::match(['get', 'post'], 'article/view', '\App\Http\Controllers\Admin\ArticleController@dataView');
    Route::match(['get', 'post'], 'article/edit', '\App\Http\Controllers\Admin\ArticleController@dataEdit');
    Route::match(['get', 'post'], 'article/add', '\App\Http\Controllers\Admin\ArticleController@dataAdd');
    Route::match(['get', 'post'], 'article/delete', '\App\Http\Controllers\Admin\ArticleController@dataDelete');

    Route::match(['get', 'post'], 'config/setting', '\App\Http\Controllers\Admin\ConfigController@setting');
    Route::match(['get', 'post'], 'config/email', '\App\Http\Controllers\Admin\ConfigController@email');
    Route::match(['get', 'post'], 'config/sms', '\App\Http\Controllers\Admin\ConfigController@sms');
    Route::match(['get', 'post'], 'config/visit', '\App\Http\Controllers\Admin\ConfigController@visit');
    Route::match(['get', 'post'], 'config/register', '\App\Http\Controllers\Admin\ConfigController@register');
    Route::match(['get', 'post'], 'config/pay_alipay', '\App\Http\Controllers\Admin\ConfigController@payAlipay');
    Route::match(['get', 'post'], 'config/pay_wechat_mobile', '\App\Http\Controllers\Admin\ConfigController@payWechatMobile');
    Route::match(['get', 'post'], 'config/pay_wechat', '\App\Http\Controllers\Admin\ConfigController@payWechat');
    Route::match(['get', 'post'], 'config/oauth_qq', '\App\Http\Controllers\Admin\ConfigController@oauthQQ');
    Route::match(['get', 'post'], 'config/oauth_wechat', '\App\Http\Controllers\Admin\ConfigController@oauthWechat');
    Route::match(['get', 'post'], 'config/oauth_weibo', '\App\Http\Controllers\Admin\ConfigController@oauthWeibo');
    Route::match(['get', 'post'], 'config/retrieve', '\App\Http\Controllers\Admin\ConfigController@retrieve');
    Route::match(['get', 'post'], 'config/contact', '\App\Http\Controllers\Admin\ConfigController@contact');
    Route::match(['get', 'post'], 'config/sso_server', '\App\Http\Controllers\Admin\ConfigController@ssoServer');
    Route::match(['get', 'post'], 'config/sso_client', '\App\Http\Controllers\Admin\ConfigController@ssoClient');

    Route::match(['get', 'post'], 'news_category/list', '\App\Http\Controllers\Admin\NewsCategoryController@dataList');
    Route::match(['get', 'post'], 'news_category/add', '\App\Http\Controllers\Admin\NewsCategoryController@dataAdd');
    Route::match(['get', 'post'], 'news_category/edit', '\App\Http\Controllers\Admin\NewsCategoryController@dataEdit');
    Route::match(['get', 'post'], 'news_category/delete', '\App\Http\Controllers\Admin\NewsCategoryController@dataDelete');
    Route::match(['get', 'post'], 'news_category/view', '\App\Http\Controllers\Admin\NewsCategoryController@dataView');
    Route::match(['get', 'post'], 'news_category/sort', '\App\Http\Controllers\Admin\NewsCategoryController@dataSort');

    Route::match(['get', 'post'], 'news/list', '\App\Http\Controllers\Admin\NewsController@dataList');
    Route::match(['get', 'post'], 'news/add', '\App\Http\Controllers\Admin\NewsController@dataAdd');
    Route::match(['get', 'post'], 'news/edit', '\App\Http\Controllers\Admin\NewsController@dataEdit');
    Route::match(['get', 'post'], 'news/delete', '\App\Http\Controllers\Admin\NewsController@dataDelete');
    Route::match(['get', 'post'], 'news/view', '\App\Http\Controllers\Admin\NewsController@dataView');

    Route::match(['get', 'post'], 'question/list', '\App\Http\Controllers\Admin\QuestionController@dataList');
    Route::match(['get', 'post'], 'question/view', '\App\Http\Controllers\Admin\QuestionController@dataView');
    Route::match(['get', 'post'], 'question/edit', '\App\Http\Controllers\Admin\QuestionController@dataEdit');
    Route::match(['get', 'post'], 'question/add', '\App\Http\Controllers\Admin\QuestionController@dataAdd');
    Route::match(['get', 'post'], 'question/delete', '\App\Http\Controllers\Admin\QuestionController@dataDelete');
    Route::match(['get', 'post'], 'question/select', '\App\Http\Controllers\Admin\QuestionController@select');
    Route::match(['get', 'post'], 'question/preview', '\App\Http\Controllers\Admin\QuestionController@preview');

    Route::match(['get', 'post'], 'question_tag/list', '\App\Http\Controllers\Admin\QuestionTagController@dataList');
    Route::match(['get', 'post'], 'question_tag/view', '\App\Http\Controllers\Admin\QuestionTagController@dataView');
    Route::match(['get', 'post'], 'question_tag/edit', '\App\Http\Controllers\Admin\QuestionTagController@dataEdit');
    Route::match(['get', 'post'], 'question_tag/add', '\App\Http\Controllers\Admin\QuestionTagController@dataAdd');
    Route::match(['get', 'post'], 'question_tag/delete', '\App\Http\Controllers\Admin\QuestionTagController@dataDelete');

    Route::match(['get', 'post'], 'question_tag_group/list', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataList');
    Route::match(['get', 'post'], 'question_tag_group/view', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataView');
    Route::match(['get', 'post'], 'question_tag_group/edit', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataEdit');
    Route::match(['get', 'post'], 'question_tag_group/add', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataAdd');
    Route::match(['get', 'post'], 'question_tag_group/delete', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataDelete');
    Route::match(['get', 'post'], 'question_tag_group/sort', '\App\Http\Controllers\Admin\QuestionTagGroupController@dataSort');

    Route::match(['get', 'post'], 'question_comment/list', '\App\Http\Controllers\Admin\QuestionCommentController@dataList');
    Route::match(['get', 'post'], 'question_comment/view', '\App\Http\Controllers\Admin\QuestionCommentController@dataView');
    Route::match(['get', 'post'], 'question_comment/edit', '\App\Http\Controllers\Admin\QuestionCommentController@dataEdit');
    Route::match(['get', 'post'], 'question_comment/add', '\App\Http\Controllers\Admin\QuestionCommentController@dataAdd');
    Route::match(['get', 'post'], 'question_comment/delete', '\App\Http\Controllers\Admin\QuestionCommentController@dataDelete');

    Route::match(['get', 'post'], 'paper_category/list', '\App\Http\Controllers\Admin\PaperCategoryController@dataList');
    Route::match(['get', 'post'], 'paper_category/add', '\App\Http\Controllers\Admin\PaperCategoryController@dataAdd');
    Route::match(['get', 'post'], 'paper_category/edit', '\App\Http\Controllers\Admin\PaperCategoryController@dataEdit');
    Route::match(['get', 'post'], 'paper_category/delete', '\App\Http\Controllers\Admin\PaperCategoryController@dataDelete');
    Route::match(['get', 'post'], 'paper_category/view', '\App\Http\Controllers\Admin\PaperCategoryController@dataView');
    Route::match(['get', 'post'], 'paper_category/sort', '\App\Http\Controllers\Admin\PaperCategoryController@dataSort');

    Route::match(['get', 'post'], 'paper/list', '\App\Http\Controllers\Admin\PaperController@dataList');
    Route::match(['get', 'post'], 'paper/view', '\App\Http\Controllers\Admin\PaperController@dataView');
    Route::match(['get', 'post'], 'paper/delete', '\App\Http\Controllers\Admin\PaperController@dataDelete');
    Route::match(['get', 'post'], 'paper/edit', '\App\Http\Controllers\Admin\PaperController@dataEdit');
    Route::match(['get', 'post'], 'paper/add', '\App\Http\Controllers\Admin\PaperController@dataAdd');
    Route::match(['get', 'post'], 'paper/download/{id}', '\App\Http\Controllers\Admin\PaperController@download');

    Route::match(['get', 'post'], 'paper_exam/list', '\App\Http\Controllers\Admin\PaperExamController@dataList');
    Route::match(['get', 'post'], 'paper_exam/view', '\App\Http\Controllers\Admin\PaperExamController@dataView');
    Route::match(['get', 'post'], 'paper_exam/delete', '\App\Http\Controllers\Admin\PaperExamController@dataDelete');
    Route::match(['get', 'post'], 'paper_exam/edit', '\App\Http\Controllers\Admin\PaperExamController@dataEdit');
    Route::match(['get', 'post'], 'paper_exam/add', '\App\Http\Controllers\Admin\PaperExamController@dataAdd');

    Route::match(['get', 'post'], 'marketing/news', '\App\Http\Controllers\Admin\MarketingController@news');

});
