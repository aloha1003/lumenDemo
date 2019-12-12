<?php
Route::post('/anchor', '\App\Http\Controllers\API\ReportController@anchor');
Route::get('/reason', '\App\Http\Controllers\API\ReportController@reason');
Route::post('/user', '\App\Http\Controllers\API\ReportController@user');
