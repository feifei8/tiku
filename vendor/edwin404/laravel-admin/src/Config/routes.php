<?php

Route::match(['get', 'post'], 'login', '\Edwin404\Admin\Http\Controllers\LoginController@index');
Route::match(['get', 'post'], 'login/flush', '\Edwin404\Admin\Http\Controllers\LoginController@flush');
Route::match(['get', 'post'], 'login/captcha', '\Edwin404\Admin\Http\Controllers\LoginController@captcha');
Route::match(['get', 'post'], 'logout', '\Edwin404\Admin\Http\Controllers\LoginController@logout');

Route::match(['get', 'post'], 'sso/client', '\Edwin404\Admin\Http\Controllers\LoginController@ssoClient');
Route::match(['get', 'post'], 'sso/server', '\Edwin404\Admin\Http\Controllers\LoginController@ssoServer');
Route::match(['get', 'post'], 'sso/server_success', '\Edwin404\Admin\Http\Controllers\LoginController@ssoServerSuccess');
Route::match(['get', 'post'], 'sso/server_logout', '\Edwin404\Admin\Http\Controllers\LoginController@ssoServerLogout');

Route::match(['get', 'post'], 'system/changepwd', '\Edwin404\Admin\Http\Controllers\SystemController@changePwd');
Route::match(['get', 'post'], 'system/clear_cache', '\Edwin404\Admin\Http\Controllers\SystemController@clearCache');

Route::match(['get', 'post'], 'system/user/role/list', '\Edwin404\Admin\Http\Controllers\SystemController@userRoleList');
Route::match(['get', 'post'], 'system/user/role/edit/{id?}', '\Edwin404\Admin\Http\Controllers\SystemController@userRoleEdit');
Route::match(['get', 'post'], 'system/user/role/delete/{id}', '\Edwin404\Admin\Http\Controllers\SystemController@userRoleDelete');

Route::match(['get', 'post'], 'system/user/list', '\Edwin404\Admin\Http\Controllers\SystemController@userList');
Route::match(['get', 'post'], 'system/user/edit/{id?}', '\Edwin404\Admin\Http\Controllers\SystemController@userEdit');
Route::match(['get', 'post'], 'system/user/delete/{id}', '\Edwin404\Admin\Http\Controllers\SystemController@userDelete');

Route::match(['get', 'post'], 'system/log/list', '\Edwin404\Admin\Http\Controllers\SystemController@logList');
Route::match(['get', 'post'], 'system/log/delete/{id}', '\Edwin404\Admin\Http\Controllers\SystemController@logDelete');

Route::match(['get', 'post'], 'system/data/image_select_dialog', '\Edwin404\Admin\Http\Controllers\DataController@imageSelectDialog');
Route::match(['get', 'post'], 'system/data/put_data_dialog/{category}', '\Edwin404\Admin\Http\Controllers\DataController@putDataDialog');
Route::match(['get', 'post'], 'system/data/select_dialog/{category}', '\Edwin404\Admin\Http\Controllers\DataController@selectDialog');
Route::match(['get', 'post'], 'system/data/temp_data_upload/{category}', '\Edwin404\Admin\Http\Controllers\DataController@tempDataUpload');
Route::match(['get', 'post'], 'system/data/ueditor', '\Edwin404\Admin\Http\Controllers\DataController@ueditorHandle');

Route::match(['get', 'post'], 'upgrade/{action}', '\Edwin404\Admin\Http\Controllers\UpgradeController@index');