<?php
Route::post('/apply', '\App\Http\Controllers\API\WithdrawApplyController@apply');
Route::get('/', '\App\Http\Controllers\API\WithdrawApplyController@index');

// 用戶提現資訊
Route::post('/user/gold/info', '\App\Http\Controllers\API\WithdrawApplyController@userGoldInfo');

// 用戶提現帳號資訊
Route::post('/user/bank/info', '\App\Http\Controllers\API\WithdrawApplyController@getBankInfo');

// 用戶新增提現帳戶資料
Route::post('/add/bank/info', '\App\Http\Controllers\API\WithdrawApplyController@addBankInfo');

// 用戶移除提現帳戶資料
Route::post('/remove/bank/info', '\App\Http\Controllers\API\WithdrawApplyController@removeBankInfo');

// 提現帳戶設為常用
Route::post('/set/bank/info/usual', '\App\Http\Controllers\API\WithdrawApplyController@setBankAsUsual');

//充值申訴
Route::post('/appeal/set', '\App\Http\Controllers\API\WithdrawApplyController@appeal');

//取得上傳申訴圖檔的token
Route::post('/appeal/photo/token', '\App\Http\Controllers\API\WithdrawApplyController@getAppealPhotoToken');
