<?php
//轮播广告
Route::post('/roll', '\App\Http\Controllers\API\RollAdController@index');
//首页
Route::post('/home', '\App\Http\Controllers\API\HomeAdController@index');
//点击广告连结，记录点击数
Route::post('/roll_hit/{id}', '\App\Http\Controllers\API\RollAdController@hit');
