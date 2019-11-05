<?php
Route::group([
    'prefix' => 'auth'

], function ($router) {

    Route::get('login', '\App\Http\Controllers\AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});