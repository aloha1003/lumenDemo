<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Code;
use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Gift\GiftGivenRequestRule;
use App\Services\GiftService;
use App\Services\LeaderboardService;

//礼物API
class GiftAPIController extends Controller
{
    private $giftService;
    private $leaderboardService;

    public function __construct(GiftService $giftService, LeaderboardService $leaderboardService)
    {
        $this->giftService = $giftService;
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * @SWG\Post(
     *     path="/gift/info",
     *     tags={"禮物"},
     *     summary="禮物資料API",
     *     description="取得所有禮物資料",
     *     operationId="info",
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
     *                  type="object",
     *                  @SWG\Property(
     *                      property="gift",
     *                      type="array",
     *                      @SWG\Items(
     *                      type="object",
     *                          @SWG\Property(property="gift_id", type="integer", example=1000001),
     *                          @SWG\Property(property="gift_name", type="string", example="Lv包包"),
     *                          @SWG\Property(property="comment", type="string", example="高級包包"),
     *                          @SWG\Property(property="price", type="double", example=10.00),
     *                          @SWG\Property(property="image", type="string", example="image/super_punch.jpg"),
     *                          @SWG\Property(property="svg", type="string", example="svg/super_punch.svg"),
     *                          @SWG\Property(property="is_big", type="integer", example=0)
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="prop",
     *                      type="array",
     *                      @SWG\Items(
     *                      type="object",
     *                          @SWG\Property(property="gift_id", type="integer", example=1000001),
     *                          @SWG\Property(property="gift_name", type="string", example="Lv包包"),
     *                          @SWG\Property(property="comment", type="string", example="高級包包"),
     *                          @SWG\Property(property="price", type="double", example=10.00),
     *                          @SWG\Property(property="image", type="string", example="image/super_punch.jpg"),
     *                          @SWG\Property(property="svg", type="string", example="svg/super_punch.svg"),
     *                          @SWG\Property(property="is_big", type="integer", example=0)
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="big_gift",
     *                      type="array",
     *                      @SWG\Items(
     *                      type="object",
     *                          @SWG\Property(property="gift_id", type="integer", example=1000001),
     *                          @SWG\Property(property="gift_name", type="string", example="Lv包包"),
     *                          @SWG\Property(property="comment", type="string", example="高級包包"),
     *                          @SWG\Property(property="price", type="double", example=10.00),
     *                          @SWG\Property(property="image", type="string", example="image/super_punch.jpg"),
     *                          @SWG\Property(property="svg", type="string", example="svg/super_punch.svg"),
     *                          @SWG\Property(property="is_big", type="integer", example=1)
     *                      )
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
    public function info()
    {
        try {
            // 取得所有禮物資料
            $collect = $this->giftService->list();

            // 依照 weight 排序, 越大的排越前面, 並轉換成array
            $collectArray = $collect->sortByDesc('weight')->toArray();

            $result = $this->giftService->arrangeListDataForGiftType($collectArray);
            // 回傳結果
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/gift/given",
     *     summary="送禮API",
     *     tags={"禮物"},
     *     description="用戶贈送禮物給其他主播",
     *     operationId="given",
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
     *     @SWG\Parameter(
     *         name="gift_id",
     *         in="formData",
     *         description="禮物id",
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
     *                  type="object",
     *                  @SWG\Property(property="remain_gold", type="decimal", example=110.25)
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
    public function given(GiftGivenRequestRule $request)
    {
        try {
            $parameters = $request->all();
            // 用 id() 來取得用戶id
            $parameters['user_id'] = id();
            \DB::beginTransaction();
            // 購買禮物
            $result = $this->giftService->purchase($parameters['user_id'], $parameters['room_id'], $parameters['gift_id']);
            \DB::commit();
            return response()->success(['remain_gold' => $result['remain_gold']], $result['status']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->error($e);
        }
    }
}
