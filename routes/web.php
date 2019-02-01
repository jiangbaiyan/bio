<?php

use Illuminate\Support\Facades\Route;

/**
 * PC端接口
 */
Route::group(['prefix' => 'pc' ], function () {
    Route::group(['prefix' => 'common'], function (){
        Route::get('login', 'Pc\Common\Login@login');
    });
});

/**
 * 网页端接口
 */
Route::group(['prefix' => 'web'], function () {

});
