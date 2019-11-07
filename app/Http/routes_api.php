<?php
Route::group([
    'prefix' => 'weixin'

], function ($router) {

    Route::get('login', '\App\Http\Controllers\AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

    Route::any('serve', '\App\Http\Controllers\Weixin\WeChatController@serve');
});