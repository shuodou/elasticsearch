<?php
use think\facade\Route;

Route::group(':version',function(){

    // es
    Route::group('channel',function(){
        // 同步渠道单个商品
        Route::post('/sync/product',':version.Channel/syncOneProduct');
        // 同步渠道商品
        Route::get('/sync/source/product',':version.Channel/syncSourceProduct');
        // 删除渠道索引
        Route::post('/del/index',':version.Channel/delIndex');
        // 删除渠道所有商品
        Route::post('/del/all/product',':version.Channel/deleteByQuery');
        // 删除渠道单个商品
        Route::post('/del/product',':version.Channel/delDoc');

        Route::get('/doc',':version.Channel/getDoc');
        Route::post('/search/product',':version.Channel/searchProducts');
        Route::post('/brand/product',':version.Channel/brandProduct');
    })->middleware(['outPut','ipCheck']);


});


