<?php
//直播列表
Route::post('/', '\App\Http\Controllers\API\LiveRoomController@index');
//批次取得所有的直播列表
Route::post('/batch', '\App\Http\Controllers\API\LiveRoomController@batch');
//開房
Route::post('/open', '\App\Http\Controllers\API\LiveRoomController@open');
//關房
Route::post('/close', '\App\Http\Controllers\API\LiveRoomController@close');
//進房
Route::post('/{id}/enter', '\App\Http\Controllers\API\LiveRoomController@enter');
//觀眾離房
Route::post('/{id}/leave', '\App\Http\Controllers\API\LiveRoomController@leave');
//取得房间讯息
Route::post('/{id}/info', '\App\Http\Controllers\API\LiveRoomController@info');
// 直播結束統計資訊
Route::post('/room/end/info', '\App\Http\Controllers\API\LiveRoomController@endInfo');
//取得房主开房连结
Route::post('/{id}/live', '\App\Http\Controllers\API\LiveRoomController@live');
//直播間內的排行榜
Route::post('/leaderboard', '\App\Http\Controllers\API\LeaderboardController@getLiveRoom');
//熱門直播間的排行榜
Route::post('/room/hot/leaderboard', '\App\Http\Controllers\API\LeaderboardController@getHotLiveRoom');
//熱門直播間排行與當前直播間排名
Route::post('/room/hot/leaderboard/with/self', '\App\Http\Controllers\API\LeaderboardController@getHotLiveRoomAndSelfRankData');
//直播間收禮統計
Route::post('/statistics/gift/info', '\App\Http\Controllers\API\LiveRoomController@getGiftStatistics');

//關房 - 內部測試
Route::post('/close/internal', '\App\Http\Controllers\API\LiveRoomController@closeForInternalTest');
