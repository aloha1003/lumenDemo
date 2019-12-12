<?php

Route::group(['middleware' => 'jwt'], function () {
    // 遊戲快速登入api
    Route::post('user/quick/login', 'GameAuthController@quickRegisterAndLogin');

    // 遊戲版本檢查 api
    Route::post('check/game/version', 'GameVersionController@checkGameVersionInfo');
});
// 遊戲下注 api
Route::post('/game/bet', 'GameController@bet');

// 遊戲結算 api
Route::post('/game/bet/settled', 'GameController@betSettled');

// 查詢金幣資訊 api
Route::post('/gold/info', 'GameUserController@checkGold');

// 遊戲上莊 api
Route::post('/bank/on', 'GameController@bankOn');

// 遊戲下莊 api
Route::post('/bank/off', 'GameController@bankOff');
