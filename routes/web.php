<?php

use Illuminate\Support\Facades\Route;

/**
 * PC端接口
 */
Route::group(['prefix' => 'pc' ], function () {
    Route::group(['prefix' => 'common'], function (){

        //登录
        Route::get('login', 'Pc\Common\Login@login');

    });

    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => 'face'], function (){

            //人脸预约
            Route::post('reserve', 'Pc\Face\Face@reserve');

            //人脸比对
            Route::post('compare', 'Pc\Face\Face@compare');
        });

    });

});

/**
 * 网页端接口
 */
Route::group(['prefix' => 'web'], function () {

});
