<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Code;
use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Barrage\BarragePurchaseRequestRule;
use App\Services\BarrageService;

//弹幕API
class BarrageAPIController extends Controller
{
    private $barrageService;
    private $userService;
    private $liveRoomService;

    public function __construct(BarrageService $barrageService)
    {
        $this->barrageService = $barrageService;
    }

    /**
     * @SWG\Post(
     *     path="/barrage/info",
     *     summary="取得彈幕資料",
     *     tags={"彈幕"},
     *     description="取得彈幕資料",
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
     *               @SWG\Property(
     *                 property="message",
     *                 type="string"
     *               ),
     *               @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="barrage_id", type="integer", example=1),
     *                      @SWG\Property(property="brrage_name", type="string", example="彈幕"),
     *                      @SWG\Property(property="comment", type="string", example="一個普通的彈幕"),
     *                      @SWG\Property(property="price", type="double", example=10.00),
     *                  )
     *               )
     *           )
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
            // 取得所有彈幕資料
            $result = $this->barrageService->list();
            // 回傳結果
            return response()->success($result);
        } catch (\Exception $e) {
            return response()->error($e);
        }
    }

    /**
     * @SWG\Post(
     *     path="/barrage/purchase",
     *     summary="購買彈幕",
     *     tags={"彈幕"},
     *     description="購買彈幕",
     *     operationId="purchase",
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
     *         name="barrage_id",
     *         in="formData",
     *         description="彈幕id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="formData",
     *         description="彈幕訊息",
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
     *                  @SWG\Property(property="remain_gold", type="decimal", example=110.25)
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *          description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function purchase(BarragePurchaseRequestRule $request)
    {
        try {
            $parameters = $request->all();

            // 用 id() 來取得用戶id
            $parameters['user_id'] = id();
            //$parameters['user_id'] = 12000000;
            \DB::beginTransaction();
            $result = $this->barrageService->purchase($parameters['user_id'], $parameters['barrage_id'], $parameters['room_id'], $parameters['message']);
            \DB::commit();
            return response()->success(['remain_gold' => $result]);

        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }
}
