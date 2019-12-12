<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\LiveRoom\BatchQueryRequest;
use App\Http\Requests\LiveRoom\EndInfoRequest;
use App\Http\Requests\LiveRoom\EnterRequest;
use App\Http\Requests\LiveRoom\GiftStatisticsRequestRule;
use App\Http\Requests\LiveRoom\OpenRequest;
use App\Http\Requests\LiveRoom\QueryRequest;
use App\Http\Requests\LiveRoom\TencentLeaveGroupRequestRule;
use App\Http\Requests\LiveRoom\TencentStreamCallbackRequestRule;
use App\Services\LiveRoom;
use Illuminate\Http\Request;

class LiveRoomController extends Controller
{
    private $service;
    public function __construct(LiveRoom $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/live",
     *     summary="直播列表",
     *     description="直播列表",
     *     operationId="index",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="query_type",
     *         in="formData",
     *         description="搜寻目标",
     *         required=true,
     *         enum={"game", "hot", "new", "follow"},
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="query_value",
     *         in="formData",
     *         description="搜寻目标, 如果是游戏的话，就代slug",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="formData",
     *         description="分页",
     *         required=false,
     *         default=1,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *               @SWG\Property(
     *                 property="message",
     *                 type="string"
     *               ),
     *               @SWG\Property(property="data", type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      ref="#/definitions/LiveRoom"
     *
     *                  )
     *               )
     *          )
     *     ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function index(QueryRequest $request)
    {
        try {
            $input = $request->only(['query_value', 'query_type', 'page']);
            $query_value = $input['query_value'] ?? "";
            $page = $input['page'] ?? 1;
            $list = $this->service->getRooms($input['query_type'], $query_value, $page);
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/batch",
     *     summary="批次取得所有直播列表",
     *     description="批次取得所有直播列表",
     *     operationId="batch",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="query_value",
     *         in="formData",
     *         description="游戏的slug",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *               @SWG\Property(
     *                 property="message",
     *                 type="string"
     *               ),
     *               @SWG\Property(property="data", type="object",
     *               @SWG\Property(
     *                  property="follow",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
    ref="#/definitions/LiveRoom"
     *                      )
     *                  ),
     *               @SWG\Property(
     *                  property="new",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                     ref="#/definitions/LiveRoom"
     *                      )
     *                  ),
    @SWG\Property(
     *                  property="game",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      ref="#/definitions/LiveRoom"
     *                      )
     *                  ),
     *               @SWG\Property(
     *                  property="hot",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      ref="#/definitions/LiveRoom",
     *                      )
     *                  ),
     *               )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function batch(BatchQueryRequest $request)
    {
        try {
            $input = $request->only(['query_value']);
            $query_value = $input['query_value'] ?? "";
            $page = 1;
            $list = $this->service->batchGetRooms($query_value, $page);
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * 騰訊直播回調測試
     */
    public function tencentStreamCallbackTest(Request $request)
    {
        //TODO
        // $input = $request->all();
        // $data = json_encode($input);
        // \Cache::forever('tencent:stream:callback', $input);
        // return response()->json([
        //     'code' => 0,
        // ]);
    }

    /**
     * 騰訊用戶離開群組回調測試
     */
    public function tencentGroupLeaveCallbackTest(Request $request)
    {
        //TODO
        // $input = $request->all();
        // $data = json_encode($input);
        // \Cache::forever('tencent:group:callback', $input);
        // return response()->json([
        //     "ActionStatus" => "OK",
        //     "ErrorInfo" => "",
        //     "ErrorCode" => 0, // 忽略应答结果
        // ]);
    }

    /**
     * 關閉直播間 - 內部測試用
     */
    public function closeForInternalTest(Request $request)
    {
        $parameters = $request->only(['room_id']);
        $this->service->closeByRoomId($parameters['room_id']);
    }

    /**
     * 騰訊直播回調api
     */
    public function tencentStreamCallback(TencentStreamCallbackRequestRule $request)
    {
        $input = $request->all();
        $sign = $input['sign'];
        $t = $input['t'];

        $tencentLiveConfig = getTencentLiveConfigFromCache()['system'];

        // 比對app id是否正確
        $liveAppId = $input['appid'];
        if ($liveAppId != $tencentLiveConfig['play_app_id']) {
            return response()->success([], 400);
        }

        // 檢查 sign 是否正確
        $selfSign = md5($tencentLiveConfig['live_call_back_key'] . $t);
        if ($selfSign != $sign) {
            return response()->success([], 400);
        }

        // 將stream id分解成 play app id 與 room id
        $stream = explode('_', $input['stream_id']);
        $appId = $stream[0];
        if ($appId != $tencentLiveConfig['play_app_id']) {
            return response()->success([], 400);
        }

        // event_type=0 斷流事件
        if ($input['event_type'] == 0) {
            $roomId = $stream[1];

            $this->service->closeByRoomId($roomId);
        }

        return response()->json([
            'code' => 0,
        ]);
    }

    /**
     * 騰訊用戶離開群組回調
     */
    public function tencentGroupLeaveCallback(TencentLeaveGroupRequestRule $request)
    {
        $input = $request->all();
        $tencentLiveConfig = getTencentLiveConfigFromCache()['system'];

        // 比對app id是否正確
        $appId = $input['SdkAppid'];
        if ($appId != $tencentLiveConfig['play_app_id']) {
            return response()->success([], 400);
        }
        $callbackCommand = $input['CallbackCommand'];
        switch ($callbackCommand) {
            case 'Group.CallbackAfterMemberExit': //观众离开房间
                $groupId = $input['GroupId'];

                $memberList = $input['ExitMemberList'];

                $idList = [];
                for ($i = 0; $i < count($memberList); $i++) {
                    $idList[] = $memberList[$i]['Member_Account'];
                }

                $this->service->leaveWithMultiUser($groupId, $idList);
                break;
            default:
                //其他的没处理
                break;
        }

        return response()->json([
            "ActionStatus" => "OK",
            "ErrorInfo" => "",
            "ErrorCode" => 0, // 忽略应答结果
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/live/open",
     *     summary="开直播",
     *      tags={"直播"},
     *     description="开直播",
     *     operationId="open",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="game_slug",
     *         in="formData",
     *         description="游戏类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="密碼",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StandResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function open(OpenRequest $request)
    {
        try {
            $input = $request->only(['game_slug', 'password']);
            $input['user_id'] = id();
            $list = $this->service->open($input);
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/live/close",
     *     summary="结束直播",
     *     description="结束直播",
     *     operationId="close",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function close()
    {
        try {
            $list = $this->service->close(id());
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/live/{roomId}/enter",
     *     summary="進入直播室",
     *     description="進入直播室",
     *     operationId="enter",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="密碼",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function enter($roomId, EnterRequest $request)
    {
        try {
            $input = $request->only(['game_slug', 'password']);
            $password = $input['password'] ?? "";
            $url = $this->service->enter($roomId, $password);
            return response()->success(['url' => $url, 'status' => true], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/{roomId}/leave",
     *     summary="離開直播室",
     *     description="離開直播室",
     *     operationId="leave",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function leave($roomId)
    {
        try {
            $this->service->leave($roomId, id());
            return response()->success(['status' => true], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/room/end/info",
     *     summary="直播結束統計資訊",
     *     description="直播結束統計資訊",
     *     operationId="endInfo",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="room_id",
     *         in="formData",
     *         description="房間id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="room_id", type="integer", example=1),
     *                      @SWG\Property(property="anchor_id", type="integer", example=12000091),
     *                      @SWG\Property(property="watch_user_number", type="integer", example=535),
     *                      @SWG\Property(property="total_income", type="double", example=10.00),
     *                      @SWG\Property(property="live_time", type="string", example="03:15:33"),
     *                      @SWG\Property(property="new_fans", type="integer", example=19),
     *                      @SWG\Property(property="game_income", type="integer", example=5000),
     *                  )
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function endInfo(EndInfoRequest $request)
    {
        try {
            $parameters = $request->all();
            // 用 id() 來取得用戶id
            $parameters['user_id'] = id();
            //$parameters['user_id'] = 12000001;

            $result = $this->service->endInfo($parameters['user_id'], $parameters['room_id']);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
    /**
     * @SWG\Post(
     *     path="/live/{roomId}/live",
     *     summary="取得房主开房连结",
     *     description="取得房主开房连结",
     *     operationId="live",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="roomId",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function live($roomId)
    {
        try {
            $url = $this->service->getSelfRoom($roomId);
            return response()->success(['url' => $url, 'status' => true], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/statistics/gift/info",
     *     summary="直播間收禮統計",
     *     description="直播間收禮統計",
     *     operationId="live",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="room_id",
     *         in="formData",
     *         description="直播間id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="name", type="integer", example="火箭"),
     *                      @SWG\Property(property="price", type="double", example=20.00),
     *                      @SWG\Property(property="count", type="integer", example=100),
     *                      @SWG\Property(property="total_gold", type="double", example=2000.00),
     *                  )
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getGiftStatistics(GiftStatisticsRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $result = $this->service->getGiftStatistics($parameters['room_id']);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/{roomId}/info",
     *     summary="取得直播间内容",
     *     description="取得直播间内容",
     *     operationId="info",
     *     tags={"直播"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */

    public function info($roomId)
    {
        try {
            $room = $this->service->info($roomId);
            return response()->success($room, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

}
