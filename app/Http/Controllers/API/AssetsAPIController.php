<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Services\GiftService;

class AssetsAPIController extends Controller
{
    private $giftService;
    public function __construct(GiftService $giftService)
    {
        $this->giftService = $giftService;
    }

    /**
     * @SWG\Get(
     *     path="/file/list",
     *     summary="熱更檔案列表api",
     *     tags={"檔案"},
     *     description="取得app熱更新要下載的檔案列表",
     *     operationId="giftAssetsList",
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
     *                      @SWG\Property(property="gift_id", type="integer", example=1),
     *                      @SWG\Property(property="local_file_name", type="string", example="000001"),
     *                      @SWG\Property(property="version", type="string", example="1567493183"),
     *                      @SWG\Property(property="image_link", type="string", example="http://www.cdn.com/image/crystal_small.jpg"),
     *                      @SWG\Property(property="svga_link", type="string", example="http://www.cdn.com/image/crystal_small.svga"),
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
    public function assetsList()
    {
        try {
            $result = $this->giftService->assetsList();

            return response()->success($result);

        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
