<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\RollAd\IndexRequest;
use App\Services\HomePageBannerService;

class HomeAdController extends Controller
{
    private $service;
    public function __construct(HomePageBannerService $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/ad/home",
     *     summary="首页广告",
     *     description="首页广告",
     *     operationId="home",
     *     tags={"广告"},
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
     *                      @SWG\Property(property="title", type="string", example=1, description="轮播广告标题"),
     *                      @SWG\Property(property="target", type="string", example="blank" , description="blank:表示为外开视窗开启网址"),
     *                      @SWG\Property(property="cover", type="string", example="http://xxx.yyy.zzz/photo.png", description="轮播图图片连结"),
     *                      @SWG\Property(property="content", type="string", example=1, description="轮播广告内容 含html 标签"),
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
    public function index(IndexRequest $request)
    {
        try {
            $input = $request->only(['platform']);
            $list = $this->service->getAdByPlatform($input['platform']);
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
