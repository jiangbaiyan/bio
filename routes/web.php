<?php

use Illuminate\Support\Facades\Route;

/**
 * PC端接口
 */
Route::group(['prefix' => 'pc' ], function () {
    Route::group(['prefix' => 'common'], function (){

        //登录
        Route::post('login', 'Pc\Common\Login@login');

    });

    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => 'face'], function (){

            //人脸预约
            Route::post('reserve', 'Pc\Face\Face@reserve');

            //人脸比对
            Route::post('compare', 'Pc\Face\Face@compare');
        });

        Route::group(['prefix' => 'finger'], function (){

            //指纹预约
            Route::post('reserve', 'Pc\Finger\Finger@reserve');

            //指纹比对
            Route::post('compare', 'Pc\Finger\Finger@compare');
        });

        Route::group(['prefix' => 'voice'], function (){

            //声纹预约
            Route::post('reserve', 'Pc\Voice\Voice@reserve');

            //声纹比对
            Route::post('compare', 'Pc\Voice\Voice@compare');
        });

    });

});

/**
 * 网页端接口
 */
Route::group(['prefix' => 'web'], function () {

});
