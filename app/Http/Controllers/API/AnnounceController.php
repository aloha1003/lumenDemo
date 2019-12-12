<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\Announce\AnnounceReadRequestRule;
use App\Http\Requests\API\RollAd\IndexRequest;
use App\Services\AnnouceService;

class AnnounceController extends Controller
{
    private $service;
    public function __construct(AnnouceService $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/announce",
     *     summary="公告",
     *     description="公告列表",
     *     operationId="index",
     *     tags={"公告"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="platform",
     *         in="formData",
     *         description="平台",
     *         required=true,
     *         enum={"ios", "android"},
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
     *                      @SWG\Property(property="id", type="integer", example=1, description="轮播广告流水号"),
     *                      @SWG\Property(property="title", type="string", example=1, description="轮播广告标题"),
     *                      @SWG\Property(property="content", type="string", example=1, description="轮播广告内容 含html 标签"),
     *                      @SWG\Property(property="start_at", type="string", example="2019-10-21 00:00:00", description="開始時間"),
     *                      @SWG\Property(property="is_read", type="integer", example=0, description="是否已讀"),
     *                      @SWG\Property(property="type_slug", type="string", example="Notice", description="Activity:活动, Topup:充值&提现, Maintain:维护, Notice:通知"),
     *                      @SWG\Property(property="is_common", type="integer", example=0, description="0: 個人公告, 1:所有公告"),
     *                    )
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
    public function index(IndexRequest $request)
    {
        try {
            $input = $request->only(['platform']);
            $list = $this->service->getListByPlatform($input['platform']);
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/announce/read",
     *     summary="閱讀公告api",
     *     description="將公告設為已讀",
     *     operationId="index",
     *     tags={"公告"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="is_common",
     *         in="formData",
     *         description="是否為公共公告, 0=>否, 1=>是",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="announce_id",
     *         in="formData",
     *         description="公告id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function read(AnnounceReadRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $userId = id();
            $isCommon = $parameters['is_common'];
            $announceId = $parameters['announce_id'];
            $this->service->read($userId, $isCommon, $announceId);
            return response()->success(['status' => true], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
