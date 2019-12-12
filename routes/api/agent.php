<?php

// 代理轉金幣給用戶
Route::post('/transfer/gold', '\App\Http\Controllers\API\AgentAPIController@transferGold');

// 代理對單一用戶的轉帳紀錄
Route::post('/transfer/history/one', '\App\Http\Controllers\API\AgentAPIController@historyOne');

// 代理的用戶名單列表
Route::post('/name/list', '\App\Http\Controllers\API\AgentAPIController@getNameList');

// 代理新增用戶名單
Route::post('/name/list/add', '\App\Http\Controllers\API\AgentAPIController@addNameList');

// 代理移除用戶名單
Route::post('/name/list/remove', '\App\Http\Controllers\API\AgentAPIController@removeNameList');

// 代理的總轉帳金額
Route::post('/transfer/gold/info', '\App\Http\Controllers\API\AgentAPIController@transferGoldInfo');

// 將用戶設為最愛
Route::post('/set/star', '\App\Http\Controllers\API\AgentAPIController@setUserStar');

// 將用戶移除最愛
Route::post('/unset/star', '\App\Http\Controllers\API\AgentAPIController@unsetUserStar');
