<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;

class TestAPIController extends Controller
{
    // private $service;

    // public function __construct(TestService $service)
    // {
    //     $this->service = $service;
    // }

    // /**
    //  * 一般api範例
    //  *
    //  * @param Lists $request
    //  * @return Response
    //  */
    // public function index(Lists $request)
    // {
    //     $data = $this->service->lists($request->all());
    //     $response = ListResource::collection($data);

    //     return response()->success($response, 200);
    // }

    // /**
    //  * redis example
    //  *
    //  * @return void
    //  */
    // public function redis()
    // {
    //     \Redis::set('name', 'jet');
    //     $defaultName = \Redis::get('name');

    //     \Redis::connection('read')->set('name', 'zgl');
    //     $tigoName = \Redis::connection('read')->get('name');

    //     \Redis::connection('write')->set('name', 'jetlin');
    //     $name = \Redis::connection('write')->get('name');
    // }

    // public function database()
    // {
    //     $user = new \App\Models\User();
    //     $user->user_type_id = '1';
    //     $user->agent_id = '1';
    //     $user->cellphone = rand(0000000000, 9999999999);
    //     $user->nickname = 'aaa';
    //     $user->sex = '1';
    //     $user->birthday = '1990-01-05';
    //     $user->avatar = 'aaa';
    //     $user->level = '1';
    //     $user->last_login_time = '2019-07-26';
    //     $user->save();
    // }

    // *
    //  * 產生 user 簽名
    //  *
    //  * @return void

    // public function userSig()
    // {
    //     dd(\Live::userSig(\Auth::user()->name));
    // }

    // /**
    //  * 產生推拉流url
    //  *
    //  * @return void
    //  */
    // public function pushPullFlow()
    // {
    //     $streamName = '1400235567_qqqqqqqq';
    //     $time = '2019-07-23 20:08:07';
    //     dd(\Live::pushPullFlow($streamName, $time));
    // }

    // /**
    //  * 查詢推斷流事件
    //  *
    //  * @return void
    //  */
    // public function history()
    // {
    //     // http://1259656042.vod2.myqcloud.com/6f94220avodcq1259656042/6833ec9a5285890791953315759/playlist_eof.m3u8
    //     dd(\Live::history([
    //         "StartTime" => "2019-07-25T0000:00Z",
    //         "EndTime" => "2019-07-26T00:00:00Z",
    //     ]));
    // }

    // /**
    //  * 斷開直播流
    //  *
    //  * @return void
    //  */
    // public function cut()
    // {
    //     // http://1259656042.vod2.myqcloud.com/6f94220avodcq1259656042/b52bf3f15285890791954311681/f0.flv
    //     dd(\Live::cut());
    // }

    // /**
    //  * 查詢正在直播資訊
    //  *
    //  * @return void
    //  */
    // public function search()
    // {
    //     // http://1259656042.vod2.myqcloud.com/6f94220avodcq1259656042/b52bf3f15285890791954311681/f0.flv
    //     dd(\Live::search());
    // }

    // public function createBigChat()
    // {
    //     // http://1259656042.vod2.myqcloud.com/6f94220avodcq1259656042/b52bf3f15285890791954311681/f0.flv
    //     dd(\Live::search());
    // }

}
