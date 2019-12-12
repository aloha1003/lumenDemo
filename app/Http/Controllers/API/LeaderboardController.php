<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\LiveRoom\HotRoomLeaderboardWithTargetRoomRequestRule;
use App\Http\Requests\LiveRoom\LeaderbordInLiveRoomRequestRule;
use App\Http\Requests\UserInfo\GetLeaderboardByPartRequestRule;
use App\Http\Requests\UserInfo\UserLeaderboardInfoByPartRequestRule;
use App\Http\Requests\UserInfo\UserLeaderboardInfoRequestRule;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    private $leaderboardService;
    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * @SWG\Post(
     *     path="/leaderboard",
     *     summary="排行榜",
     *     description="排行榜",
     *     operationId="getTotal",
     *     tags={"排行榜"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
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
     *               @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="anchor", type="object",
     *                      @SWG\Property(property="day", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="week", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="month", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="all", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      )
     *                  ),
     *                  @SWG\Property(property="fans", type="object",
     *                      @SWG\Property(property="day", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="week", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="month", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      ),
     *                      @SWG\Property(property="all", type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(property="user_id", type="string", example=12000003),
     *                              @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                              @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                              @SWG\Property(property="level", type="string", example=0),
     *                              @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                              @SWG\Property(property="sex", type="string", example=0),
     *                              @SWG\Property(property="price", type="string", example="30.5"),
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getTotal()
    {
        try {
            $result = $this->leaderboardService->getAllTotalLeaderboardData();
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/leaderboard/by/part",
     *     summary="取得部份排行榜",
     *     description="取得部份排行榜",
     *     operationId="getByTypeAnRange",
     *     tags={"排行榜"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="請輸入 anchor, fans",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="date_range",
     *         in="formData",
     *         description="請輸入 day, week, month, all",
     *         required=false,
     *         type="integer"
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
     *                   @SWG\Items(
     *                   type="object",
     *                   @SWG\Property(property="user_id", type="string", example=12000003),
     *                   @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                   @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                   @SWG\Property(property="level", type="string", example=0),
     *                   @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                   @SWG\Property(property="sex", type="string", example=0),
     *                   @SWG\Property(property="price", type="string", example="30.5")
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getByTypeAndRange(GetLeaderboardByPartRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $type = $parameters['type'];
            $range = $parameters['date_range'];
            $result = $this->leaderboardService->getLeaderboardDataByTypeAndDateRange($type, $range);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/leaderboard/info",
     *     summary="用戶排行榜資訊",
     *     description="查詢指定用戶的排行榜資訊",
     *     operationId="getPersonal",
     *     tags={"排行榜"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要搜尋的用戶id, 若不帶, 則預設抓token登入的用戶資料",
     *         required=false,
     *         type="integer"
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
     *               @SWG\Property(property="data", type="object",
     *                  @SWG\Property(property="day", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="string", example=12000003),
     *                          @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                          @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                          @SWG\Property(property="level", type="string", example=0),
     *                          @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                          @SWG\Property(property="sex", type="string", example=0),
     *                          @SWG\Property(property="price", type="string", example="30.5"),
     *                      )
     *                  ),
     *                  @SWG\Property(property="week", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="string", example=12000003),
     *                          @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                          @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                          @SWG\Property(property="level", type="string", example=0),
     *                          @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                          @SWG\Property(property="sex", type="string", example=0),
     *                          @SWG\Property(property="price", type="string", example="30.5"),
     *                      )
     *                  ),
     *                  @SWG\Property(property="month", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="string", example=12000003),
     *                          @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                          @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                          @SWG\Property(property="level", type="string", example=0),
     *                          @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                          @SWG\Property(property="sex", type="string", example=0),
     *                          @SWG\Property(property="price", type="string", example="30.5"),
     *                      )
     *                  ),
     *                  @SWG\Property(property="all", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="string", example=12000003),
     *                          @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                          @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                          @SWG\Property(property="level", type="string", example=0),
     *                          @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                          @SWG\Property(property="sex", type="string", example=0),
     *                          @SWG\Property(property="price", type="string", example="30.5"),
     *                      )
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getPersonal(UserLeaderboardInfoRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $targetId = id();
            if (isset($parameters['target_user_id'])) {
                $targetId = $parameters['target_user_id'];
            }
            $result = $this->leaderboardService->getPersonalLeaderboardData($targetId);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/user/leaderboard/info/by/part",
     *     summary="取得部份 個人粉絲排行榜",
     *     description="取得部份 個人粉絲排行榜",
     *     operationId="getPersonalByTypeAndRange",
     *     tags={"排行榜"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要搜尋的用戶id, 若不帶, 則預設抓token登入的用戶資料",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="date_range",
     *         in="formData",
     *         description="請輸入 day, week, month, all",
     *         required=false,
     *         type="integer"
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
     *                   @SWG\Items(
     *                   type="object",
     *                   @SWG\Property(property="user_id", type="string", example=12000003),
     *                   @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                   @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                   @SWG\Property(property="level", type="string", example=0),
     *                   @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                   @SWG\Property(property="sex", type="string", example=0),
     *                   @SWG\Property(property="price", type="string", example="30.5")
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getPersonalByTypeAndRange(UserLeaderboardInfoByPartRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $targetId = id();
            if (isset($parameters['target_user_id'])) {
                $targetId = $parameters['target_user_id'];
            }
            $result = $this->leaderboardService->getPersonalLeaderboardDataByDateRange($targetId, $parameters['date_range']);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/live/leaderboard",
     *     summary="直播間內的用戶消費排行榜資訊",
     *     description="直播間內的用戶消費排行榜資訊",
     *     operationId="getLiveRoom",
     *     tags={"排行榜"},
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
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="anchor_id",
     *         in="formData",
     *         description="主播id",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="number",
     *         in="formData",
     *         description="取得的資料筆數, 最少5, 最多50",
     *         required=false,
     *         type="integer"
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
     *                      @SWG\Property(property="user_id", type="string", example=12000003),
     *                      @SWG\Property(property="pretty_id", type="string", example=12000003),
     *                      @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                      @SWG\Property(property="level", type="string", example=0),
     *                      @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                      @SWG\Property(property="sex", type="string", example=0),
     *                      @SWG\Property(property="price", type="string", example="30.5"),
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getLiveRoom(LeaderbordInLiveRoomRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $targetId = id();

            $roomId = $parameters['room_id'];
            $anchorId = $parameters['anchor_id'];
            $number = $parameters['number'];

            $result = $this->leaderboardService->getLiveRoomLeaderboardData($anchorId, $roomId, $number);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/live/room/hot/leaderboard",
     *     summary="熱門直播排行榜",
     *     description="熱門直播排行榜",
     *     operationId="getHotLiveRoom",
     *     tags={"排行榜"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
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
     *                      @SWG\Property(property="room_id", type="integer", example=55),
     *                      @SWG\Property(property="user_id", type="integer", example=12000003),
     *                      @SWG\Property(property="pretty_id", type="integer", example=12000003),
     *                      @SWG\Property(property="hot_value", type="integer", example=50000),
     *                      @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                      @SWG\Property(property="level", type="string", example=0),
     *                      @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                      @SWG\Property(property="sex", type="string", example=0),
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getHotLiveRoom()
    {
        try {
            $result = $this->leaderboardService->getHotLiveRoomLeaderboardData();
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/live/room/hot/leaderboard/with/self",
     *     summary="熱門直播間排行榜與指定的直播間排名資料",
     *     description="熱門直播間排行榜與指定的直播間排名資料",
     *     operationId="getHotLiveRoomAndSelfRankData",
     *     tags={"排行榜"},
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
     *         required=true,
     *         type="integer"
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
     *
     *               @SWG\Property(property="data", type="object",
     *                    @SWG\Property(property="target", type="object",
     *                      @SWG\Property(property="rank", type="integer", example=103),
     *                      @SWG\Property(property="room_id", type="integer", example=55),
     *                      @SWG\Property(property="user_id", type="integer", example=12000003),
     *                      @SWG\Property(property="pretty_id", type="integer", example=12000003),
     *                      @SWG\Property(property="hot_value", type="integer", example=50000),
     *                      @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                      @SWG\Property(property="level", type="string", example=0),
     *                      @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                      @SWG\Property(property="sex", type="string", example=0),
     *                    ),
     *                    @SWG\Property(property="leaderboard", type="array",
     *                      @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="room_id", type="integer", example=55),
     *                      @SWG\Property(property="user_id", type="integer", example=12000003),
     *                      @SWG\Property(property="pretty_id", type="integer", example=12000003),
     *                      @SWG\Property(property="hot_value", type="integer", example=50000),
     *                      @SWG\Property(property="avatar", type="string", example="http://pww0o6ms4.bkt.clouddn.com/img/avatar"),
     *                      @SWG\Property(property="level", type="string", example=0),
     *                      @SWG\Property(property="nick_name", type="string", example="xxx"),
     *                      @SWG\Property(property="sex", type="string", example=0),
     *                  )
     *              ))
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getHotLiveRoomAndSelfRankData(HotRoomLeaderboardWithTargetRoomRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $roomId = $parameters['room_id'];
            $result = $this->leaderboardService->getHotLiveRoomLeaderboardAndTargetRoomRankData($roomId);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }
}
