<?php
//直播列表
Route::post('/sdk/signature', '\App\Http\Controllers\API\LoginAPIController@signature');

//用戶基本資訊
Route::post('/basic/info', 'UserInfoAPIController@basicInfo');

// 用戶搜尋
Route::post('/search', 'UserInfoAPIController@search');

// 關注用戶
Route::post('/follow', 'UserInfoAPIController@follow');

// 取消關注用戶
Route::post('/follow/cancel', 'UserInfoAPIController@followCancel');

// 檢查指定用戶是否已關注
Route::post('/follow/check', 'UserInfoAPIController@followCheck');

//用戶詳細資訊
Route::post('/detail/info', 'UserInfoAPIController@detailInfo');

//用戶排行榜資訊
Route::post('/leaderboard/info', 'LeaderboardController@getPersonal');

//用戶排行榜資訊 - 部份
Route::post('/leaderboard/info/by/part', 'LeaderboardController@getPersonalByTypeAndRange');

//編輯用戶暱稱
Route::post('/edit/nickname', 'UserInfoAPIController@editNickname');

//編輯用戶性別
Route::post('/edit/sex', 'UserInfoAPIController@editSex');

//編輯用戶生日
Route::post('/edit/birthday', 'UserInfoAPIController@editBirthday');

//編輯用戶簽名狀態
Route::post('/edit/sign', 'UserInfoAPIController@editSign');

//編輯用戶簡介
Route::post('/edit/intro', 'UserInfoAPIController@editIntro');

//編輯用戶頭像
//Route::post('/edit/avatar', 'UserInfoAPIController@editAvatar');

// 取得頭像上傳的token
Route::post('/avatar/token', 'UserInfoAPIController@getAvatarToken');

// 設置頭像的url到db裡面
Route::post('/avatar/url/set', 'UserInfoAPIController@setAvatarUrl');

// 取得用戶頭像可更新次數
Route::post('/avatar/change/times', 'UserInfoAPIController@getAvatarChangeTimes');

// 取得封面圖上傳的token
Route::post('/frontcover/token', 'UserInfoAPIController@getFrontcoverToken');

// 取得封面圖的url到db裡面
Route::post('/frontcover/url/set', 'UserInfoAPIController@setFrontcoverUrl');

// 上傳檔案, 測試用
Route::post('/avatar/upload', 'UserInfoAPIController@avatarUpload');

// 寄修改密碼sms到用戶手機
Route::post('/password/sms/send', 'UserInfoAPIController@sendChangePasswordSms');

// 使用sms驗證碼修改密碼
Route::post('/password/set/by/sms', 'UserInfoAPIController@setPasswordBySms');

// 使用原有密碼修改密碼
Route::post('/password/set', 'UserInfoAPIController@setPassword');

// 用戶意見反饋
Route::post('/feedback/set', 'UserInfoAPIController@setFeedback');

//用户申请实名认证
Route::post('/real_name_apply', 'UserInfoAPIController@realNameApply');

// 意見反饋類型列表
Route::get('/feedback/type/list', 'UserInfoAPIController@feedbackTypeList');

// 用戶黑名單列表
Route::post('/black/list', 'UserInfoAPIController@blackList');

// 將其他用戶加到黑名單
Route::post('/add/black/', 'UserInfoAPIController@addBlack');

// 將其他用戶解除黑名單
Route::post('/remove/black/', 'UserInfoAPIController@removeBlack');

// 用戶新增動態
Route::post('/story/post', 'UserInfoAPIController@postStory');

// 取得動態圖片上傳的token
Route::post('/story/photo/token', 'UserInfoAPIController@getStoryPhotoToken');

// 取得用戶的動態列表
Route::post('/story/list', 'UserInfoAPIController@getStoryList');

// 用戶移除動態
Route::post('/story/remove', 'UserInfoAPIController@removeStory');

// 用戶編輯動態
Route::post('/story/edit', 'UserInfoAPIController@editStory');

// 用戶開播預告 - 新增
Route::post('/schedule/add', 'UserInfoAPIController@addLiveSchedule');

// 用戶開播預告 - 刪除
Route::post('/schedule/remove', 'UserInfoAPIController@removeLiveSchedule');

// 用戶開播預告 - 列表
Route::post('/schedule/list', 'UserInfoAPIController@getLiveSchedule');

// 取得個人動態主圖片的上傳token
Route::post('/story/main_photo/token', 'UserInfoAPIController@getStoryMainPhotoToken');

// 設置個人動態主圖片的url到db裡面
Route::post('/story/main_photo/url/set', 'UserInfoAPIController@setStoryMainPhotoUrl');
