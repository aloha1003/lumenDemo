<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Models\Maintain as MaintainModel;
use App\Services\SystemService;
use Carbon\Carbon;

class SystemAPIController extends Controller
{
    public $systemService;
    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    /**
     * @SWG\Get(
     *     path="/system/config",
     *     tags={"系統"},
     *     summary="系統參數API",
     *     description="取得系統參數資料",
     *     operationId="getConfig",
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
     *                  @SWG\Property(property="coinRatio", type="integer", example=30000),
     *                  @SWG\Property(property="withDrawFrontShow", type="string", example="Y"),
     *                  @SWG\Property(property="anchorReportReason", type="string", example="json string"),
     *                  @SWG\Property(property="feedbackType", type="string", example="json string"),
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
    public function getConfig()
    {
        try {
            $result = $this->systemService->getConfigForFront();
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Get(
     *     path="/bank/list",
     *     tags={"系統"},
     *     summary="銀行列表API",
     *     description="取得銀行列表",
     *     operationId="getBank",
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
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="id", type="integer", example=30000),
     *                      @SWG\Property(property="bank_slug", type="string", example="bank100001"),
     *                      @SWG\Property(property="bank_name", type="string", example="工商银行"),
     *                      @SWG\Property(property="icon_url", type="string", example="json string"),
     *                      @SWG\Property(property="created_at", type="string", example="2019-12-05 12:00:48"),
     *                      @SWG\Property(property="updated_at", type="string", example="2019-12-05 12:00:48")
     *                  )
     *               )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getBank()
    {
        $result = \Cache::get(\App\Models\BaseBankList::CACHE_KEY);
        return response()->success($result);
    }

    /**
     * @SWG\Get(
     *     path="/maintain/info",
     *     tags={"系統"},
     *     summary="維護api",
     *     description="維護api, switch:0(不維護),1(維護)   ;   platform: 0(都不維護), 1(ios維護), 2(android維護), 3(全平台維護)",
     *     operationId="getMaintain",
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
     *              @SWG\Property(property="data", type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="switch", type="string", example=1),
     *                      @SWG\Property(property="platform", type="string", example=3),
     *                      @SWG\Property(property="start_datetime", type="string", example="2019-12-05 12:00:48"),
     *                      @SWG\Property(property="end_datetime", type="string", example="2019-12-05 12:00:48"),
     *                      @SWG\Property(property="front_comment", type="string", example="系統維護中"),
     *                  )
     *               )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getMaintain()
    {
        $data = \Cache::get(\App\Models\Maintain::CACHE_KEY);
        $data['platform'] = (int) $data['platform'];

        $now = Carbon::now();
        $startTime = Carbon::parse($data['start_datetime']);
        $endTime = Carbon::parse($data['end_datetime']);

        if ($data['switch'] == MaintainModel::MAINTAIN_SWITCH_ON &&
            ($now >= $startTime && $now <= $endTime) &&
            $data['platform'] != MaintainModel::PLATFORM_NONE
        ) {
            $result = [
                'switch' => $data['switch'],
                'platform' => $data['platform'],
                'start_datetime' => $data['start_datetime'],
                'end_datetime' => $data['end_datetime'],
                'front_comment' => $data['front_comment'],
            ];
        } else {
            $result = [
                'switch' => MaintainModel::MAINTAIN_SWITCH_OFF,
                'platform' => '',
                'start_datetime' => '',
                'end_datetime' => '',
                'front_comment' => '',
            ];
        }

        return response()->success($result);
    }

}
