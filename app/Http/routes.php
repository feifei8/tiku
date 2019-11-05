<?php

Route::group(
    [
        'middleware' => [
            \Edwin404\Base\Support\BaseMiddleware::class,
            \App\Http\Middleware\MemberAuth::class,
        ]
    ],
    function () {

        Route::match(['get', 'post'], '', '\App\Http\Controllers\Main\IndexController@index');

        Route::match(['get', 'post'], 'login', '\App\Http\Controllers\Main\IndexController@login');
        Route::match(['get', 'post'], 'logout', '\App\Http\Controllers\Main\IndexController@logout');
        Route::match(['get', 'post'], 'register', '\App\Http\Controllers\Main\IndexController@register');
        Route::match(['get', 'post'], 'register/username', '\App\Http\Controllers\Main\IndexController@registerUsername');
        Route::match(['get', 'post'], 'register/phone', '\App\Http\Controllers\Main\IndexController@registerPhone');
        Route::match(['get', 'post'], 'register/phone_verify', '\App\Http\Controllers\Main\IndexController@registerPhoneVerify');
        Route::match(['get', 'post'], 'register/email', '\App\Http\Controllers\Main\IndexController@registerEmail');
        Route::match(['get', 'post'], 'register/email_verify', '\App\Http\Controllers\Main\IndexController@registerEmailVerify');
        Route::match(['get', 'post'], 'register/captcha', '\App\Http\Controllers\Main\IndexController@registerCaptcha');
        Route::match(['get', 'post'], 'register/bind', '\App\Http\Controllers\Main\IndexController@registerBind');
        Route::match(['get', 'post'], 'retrieve', '\App\Http\Controllers\Main\IndexController@retrieve');
        Route::match(['get', 'post'], 'retrieve/email', '\App\Http\Controllers\Main\IndexController@retrieveEmail');
        Route::match(['get', 'post'], 'retrieve/email_verify', '\App\Http\Controllers\Main\IndexController@retrieveEmailVerify');
        Route::match(['get', 'post'], 'retrieve/phone', '\App\Http\Controllers\Main\IndexController@retrievePhone');
        Route::match(['get', 'post'], 'retrieve/phone_verify', '\App\Http\Controllers\Main\IndexController@retrievePhoneVerify');
        Route::match(['get', 'post'], 'retrieve/captcha', '\App\Http\Controllers\Main\IndexController@retrieveCaptcha');
        Route::match(['get', 'post'], 'retrieve/reset', '\App\Http\Controllers\Main\IndexController@retrieveReset');
        Route::match(['get', 'post'], 'oauth_login_{oauthType}', '\App\Http\Controllers\Main\IndexController@oauthLogin');
        Route::match(['get', 'post'], 'oauth_callback_{oauthType}', '\App\Http\Controllers\Main\IndexController@oauthCallback');
        Route::match(['get', 'post'], 'oauth_bind_{oauthType}', '\App\Http\Controllers\Main\IndexController@oauthBind');

        Route::get('sso/client', '\App\Http\Controllers\Main\IndexController@ssoClient');
        Route::get('sso/server', '\App\Http\Controllers\Main\IndexController@ssoServer');
        Route::get('sso/server_success', '\App\Http\Controllers\Main\IndexController@ssoServerSuccess');
        Route::get('sso/server_logout', '\App\Http\Controllers\Main\IndexController@ssoServerLogout');

        Route::match(['get', 'post'], 'data/image_select_dialog', '\App\Http\Controllers\Main\DataController@imageSelectDialog');
        Route::match(['get', 'post'], 'data/temp_upload/{category}', '\App\Http\Controllers\Main\DataController@tempUpload');

        Route::match(['get', 'post'], 'member', '\App\Http\Controllers\Main\MemberController@index');

        Route::match(['get', 'post'], 'member/profile_captcha', '\App\Http\Controllers\Main\MemberProfileController@captcha');
        Route::match(['get', 'post'], 'member/profile', '\App\Http\Controllers\Main\MemberProfileController@index');
        Route::match(['get', 'post'], 'member/profile_basic', '\App\Http\Controllers\Main\MemberProfileController@basic');
        Route::match(['get', 'post'], 'member/profile_password', '\App\Http\Controllers\Main\MemberProfileController@password');
        Route::match(['get', 'post'], 'member/profile_avatar', '\App\Http\Controllers\Main\MemberProfileController@avatar');
        Route::match(['get', 'post'], 'member/profile_email', '\App\Http\Controllers\Main\MemberProfileController@email');
        Route::match(['get', 'post'], 'member/profile_email_verify', '\App\Http\Controllers\Main\MemberProfileController@emailVerify');
        Route::match(['get', 'post'], 'member/profile_phone', '\App\Http\Controllers\Main\MemberProfileController@phone');
        Route::match(['get', 'post'], 'member/profile_phone_verify', '\App\Http\Controllers\Main\MemberProfileController@phoneVerify');

        Route::match(['get', 'post'], 'member/favorite_submit', '\App\Http\Controllers\Main\MemberFavoriteController@submit');

        Route::match(['get', 'post'], 'member/message', '\App\Http\Controllers\Main\MemberMessageController@index');
        Route::match(['get', 'post'], 'member/message_mark_read', '\App\Http\Controllers\Main\MemberMessageController@markRead');
        Route::match(['get', 'post'], 'member/message_mark_read_all', '\App\Http\Controllers\Main\MemberMessageController@markReadAll');

        Route::match(['get', 'post'], 'member/exam', '\App\Http\Controllers\Main\MemberExamController@index');
        Route::match(['get', 'post'], 'member/exam/{id}', '\App\Http\Controllers\Main\MemberExamController@view')->where(['id' => '[0-9]+']);

        Route::match(['get', 'post'], 'member/favorite_question', '\App\Http\Controllers\Main\MemberFavoriteController@question');

        Route::match(['get', 'post'], 'search', '\App\Http\Controllers\Main\SearchController@index');
        Route::match(['get', 'post'], 'search/question', '\App\Http\Controllers\Main\SearchController@question');
        Route::match(['get', 'post'], 'search/paper', '\App\Http\Controllers\Main\SearchController@paper');

        Route::match(['get', 'post'], 'article/{id}', '\App\Http\Controllers\Main\ArticleController@index')->where(['id' => '[0-9]+']);

        Route::match(['get', 'post'], 'news', '\App\Http\Controllers\Main\NewsController@index');
        Route::match(['get', 'post'], 'news/{id}', '\App\Http\Controllers\Main\NewsController@view')->where(['id' => '[0-9]+']);

        Route::match(['get', 'post'], 'tags', '\App\Http\Controllers\Main\TagsController@index');

        Route::match(['get', 'post'], 'question', '\App\Http\Controllers\Main\QuestionController@index');
        Route::match(['get', 'post'], 'question/list/{tags?}', '\App\Http\Controllers\Main\QuestionController@lists');
        Route::match(['get', 'post'], 'question/view/{alias}', '\App\Http\Controllers\Main\QuestionController@view')->where(['alias' => '[a-z0-9]+']);
        Route::match(['get', 'post'], 'question/comment_post/{alias}', '\App\Http\Controllers\Main\QuestionController@commentPost')->where(['alias' => '[a-z0-9]+']);
        Route::match(['get', 'post'], 'question/comment_delete/{id}', '\App\Http\Controllers\Main\QuestionController@commentDelete')->where(['id' => '[0-9]+']);
        Route::match(['get', 'post'], 'question/stat_correct/{alias}', '\App\Http\Controllers\Main\QuestionController@statCorrect')->where(['alias' => '[a-z0-9]+']);
        Route::match(['get', 'post'], 'question/stat_incorrect/{alias}', '\App\Http\Controllers\Main\QuestionController@statIncorrect')->where(['alias' => '[a-z0-9]+']);

        Route::match(['get', 'post'], 'paper', '\App\Http\Controllers\Main\PaperController@index');
        Route::match(['get', 'post'], 'paper/view/{alias}', '\App\Http\Controllers\Main\PaperController@view');
        Route::match(['get', 'post'], 'paper/exam/{alias}', '\App\Http\Controllers\Main\PaperExamController@index');
        Route::match(['get', 'post'], 'paper/exam_start/{alias}', '\App\Http\Controllers\Main\PaperExamController@start');
        Route::match(['get', 'post'], 'paper/exam_submit/{alias}', '\App\Http\Controllers\Main\PaperExamController@submit');
        Route::match(['get', 'post'], 'paper/exam_save/{alias}', '\App\Http\Controllers\Main\PaperExamController@save');

        Route::match(['get', 'post'], 'marketing_schedule', '\App\Http\Controllers\Main\MarketingScheduleController@index');

    }
);

if (file_exists(__DIR__ . '/routes_custom.php')) {
    include __DIR__ . '/routes_custom.php';
}

Route::match(['get', 'post'], 'placeholder/{width}x{height}', '\Edwin404\Placeholder\Controllers\PlaceholderController@index');
Route::match(['get', 'post'], 'install/ping', '\Edwin404\Tecmz\Controllers\InstallController@ping');
Route::match(['get', 'post'], 'install/execute', '\Edwin404\Tecmz\Controllers\InstallController@execute');
Route::match(['get', 'post'], 'install/lock', '\Edwin404\Tecmz\Controllers\InstallController@lock');

Route::match(['get', 'post'], 'pay/return/{payType}', '\Edwin404\Pay\Controllers\ReturnController@index');
Route::match(['get', 'post'], 'pay/notify/{payType}', '\Edwin404\Pay\Controllers\NotifyController@index');

Route::match(['get', 'post'], 'detect_device', '\App\Http\Controllers\Main\IndexController@detectDevice');

include __DIR__ . '/routes_admin.php';
include __DIR__ . '/routes_api.php';
