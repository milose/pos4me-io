<?php

use App\Operater;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;
use App\Auth\Login;

/*
    Login
 */
Route::post('/login/basic', 'LoginController@basic');
Route::post('/login/ean', 'LoginController@ean');

/*
    API
 */
Route::group(['middleware' => 'token'], function () {
    Route::get('', function() {
        return request()->operater;
    });
});