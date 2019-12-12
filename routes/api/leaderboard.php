<?php

Route::post('/', '\App\Http\Controllers\API\LeaderboardController@getTotal');

Route::post('/by/part', '\App\Http\Controllers\API\LeaderboardController@getByTypeAndRange');
