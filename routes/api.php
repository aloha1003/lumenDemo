<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::options('*', function (Request $request) {
    return response('success');
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/user', ['middleware' => 'auth:api'], function (Request $request) {
    return $request->user();
});
// 用戶註冊簡訊碼傳送
Route::post('user/register/sms/send', 'RegisterAPIController@sms');

// 用戶註冊簡訊碼驗證
Route::post('user/register/sms/validate', 'RegisterAPIController@smsValidate');

// 用戶註冊
Route::post('user/register', 'RegisterAPIController@register');

// id登入
Route::post('user/id/login', 'LoginAPIController@loginID');

// 手機登入
Route::post('user/cellphone/login', 'LoginAPIController@loginCellphone');

// 檔案清單
Route::get('file/list', 'AssetsAPIController@assetsList');

// 版本資訊
Route::post('app/version', 'AppVersionController@getAll');

// 系統參數
Route::get('system/config', 'SystemAPIController@getConfig');

// 銀行資訊
Route::get('bank/list', 'SystemAPIController@getBank');

// 發送忘記密碼的sms驗證碼
Route::post('/forget/password/sms/send', 'UserInfoAPIController@sendForgetPasswordSms');

// 驗證忘記密碼的驗證碼
Route::post('/forget/password/sms/validate', 'UserInfoAPIController@forgetPasswordSmsValidate');

//Proxy更新下载连结
Route::get('/release', 'ReleaseAPIController@index');
Route::group(['middleware' => 'jwt'], function () {
    //使用者
    Route::group(['prefix' => 'user'], function ($router) {
        require base_path('routes/api/user.php');
    });
    Route::get('test/user-sig', 'TestAPIController@userSig');
    Route::get('test/push-pull-flow', 'TestAPIController@pushPullFlow');
    Route::get('test/history', 'TestAPIController@history');
    Route::get('test/cut', 'TestAPIController@cut');
    Route::get('test/redis', 'TestAPIController@redis');
    Route::get('test/database', 'TestAPIController@database');
    Route::get('test/search', 'TestAPIController@search');

    //直播
    Route::group(['prefix' => 'live'], function ($router) {
        require base_path('routes/api/live.php');
    });
    //礼物
    Route::group(['prefix' => 'gift'], function ($router) {
        require base_path('routes/api/gift.php');
    });
    //弹幕
    Route::group(['prefix' => 'barrage'], function ($router) {
        require base_path('routes/api/barrage.php');
    });
    //游戏
    Route::group(['prefix' => 'game'], function ($router) {
        require base_path('routes/api/game.php');
    });
    //广告
    Route::group(['prefix' => 'ad'], function ($router) {
        require base_path('routes/api/ad.php');
    });
    //广告
    Route::group(['prefix' => 'announce'], function ($router) {
        require base_path('routes/api/announce.php');
    });

    //举报主播
    Route::group(['prefix' => 'report'], function ($router) {
        require base_path('routes/api/report.php');
    });

    //提现
    Route::group(['prefix' => 'withdraw'], function ($router) {
        require base_path('routes/api/withdraw.php');
    });
    //IM
    Route::group(['prefix' => 'im'], function ($router) {
        require base_path('routes/api/im.php');
    });
    //充值
    Route::group(['prefix' => 'topup'], function ($router) {
        require base_path('routes/api/topup.php');
    });
    //代理
    Route::group(['prefix' => 'agent'], function ($router) {
        require base_path('routes/api/agent.php');
    });

    //排行榜
    Route::group(['prefix' => 'leaderboard'], function ($router) {
        require base_path('routes/api/leaderboard.php');
    });

    //设备
    Route::group(['prefix' => 'device'], function ($router) {
        require base_path('routes/api/device.php');
    });

});
