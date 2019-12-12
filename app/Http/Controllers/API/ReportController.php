<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\Report\AnchorRequest;
use App\Http\Requests\API\Report\UserRequest;
use App\Services\ReportService;

class ReportController extends Controller
{
    private $service;
    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/report/anchor",
     *     summary="举报主播",
     *     description="举报主播",
     *     operationId="anchor",
     *     tags={"举报"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="anchor_user_id",
     *         in="formData",
     *         description="主播Id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="reason_slug",
     *         in="formData",
     *         description="举报原因slug, 请参考",
     *         required=true,
     *         enum={"dont_like", "porn", "political", "bilk"},
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
    public function anchor(AnchorRequest $request)
    {
        try {
            $input = $request->only(['anchor_user_id', 'reason_slug']);
            $input['report_user_id'] = id();
            $this->service->reprtAnchor($input);
            return response()->success([], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/report/user",
     *     summary="举报用戶",
     *     description="举报用戶",
     *     operationId="user",
     *     tags={"举报"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="user_id",
     *         in="formData",
     *         description="用戶Id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="reason_slug",
     *         in="formData",
     *         description="举报原因slug, 请参考",
     *         required=true,
     *         enum={"porn", "religion", "direct_sales", "business", "bilk", "fake", "government", "illegal"},
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
    public function user(UserRequest $request)
    {
        try {
            $input = $request->only(['user_id', 'reason_slug']);
            $data = [
                'report_user_id' => id(),
                'reported_user_id' => $input['user_id'],
                'reason_slug' => $input['reason_slug'],
            ];
            $this->service->reportUser($data);
            return response()->success([], 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Get(
     *     path="/report/reason",
     *     summary="举报的理由",
     *     description="举报的理由",
     *     operationId="reason",
     *     tags={"举报"},
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
     *                      @SWG\Property(property="reason_slug", type="string", example="dont_like", description="举报理由识别码"),
     *                      @SWG\Property(property="title", type="string", example="不喜欢这个直播", description="举报理由的显示标题"),
     *                  )
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
    public function reason()
    {
        try {
            $reasons = sc('userReportReason');
            $formatReason = [];
            if ($reasons == null || $reasons == []) {
                return response()->success([], 200);
            }
            foreach ($reasons as $reason_slug => $title) {
                $formatReason[] = [
                    'reason_slug' => $reason_slug,
                    'title' => $title,
                ];
            }
            return response()->success($formatReason, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
