<?php
//交易方式列表
Route::post('/pay_list', '\App\Http\Controllers\API\TopupController@payList');
//执行交易
Route::post('/pay', '\App\Http\Controllers\API\TopupController@pay');
//交易纪录列表
Route::post('/record_list', '\App\Http\Controllers\API\TopupController@recordList');
//交易内容
Route::post('/record/{transaction_no}', '\App\Http\Controllers\API\TopupController@record');
//訂單申訴
Route::post('/appeal/set', '\App\Http\Controllers\API\TopupController@appeal');
//取得上傳申訴圖檔的token
Route::post('/appeal/photo/token', '\App\Http\Controllers\API\TopupController@getAppealPhotoToken');
