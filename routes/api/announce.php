<?php
//公告页
Route::post('/', '\App\Http\Controllers\API\AnnounceController@index');
//
Route::post('/no_show_again', '\App\Http\Controllers\API\AnnounceController@index');

//已讀
Route::post('/read', '\App\Http\Controllers\API\AnnounceController@read');
