<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Services\GameService;

class GameController extends Controller
{
    private $service;
    public function __construct(GameService $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/game",
     *     summary="游戏列表",
     *     description="游戏列表",
     *     operationId="index",
     *     tags={"游戏"},
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
     *                      @SWG\Property(property="name", type="string", example=1, description="轮播广告标题"),
     *                      @SWG\Property(property="slug", type="string", example="21" , description="游戏唯一识别，用来选择何种直播游戏"),
     *                      @SWG\Property(property="game_app_id", type="string", example="21" , description="对应app上的游戏的id"),
     *                      @SWG\Property(property="cover_ios", type="string",  example="http://xxx.yyy.zzz/photo.png", description="游戏封面图"),
     *                      @SWG\Property(property="cover_android", type="string",  example="http://xxx.yyy.zzz/photo.png", description="游戏封面图"),
     *                      @SWG\Property(property="rectangle_cover_ios", type="string",  example="http://xxx.yyy.zzz/photo.png", description="游戏長形图"),
     *                      @SWG\Property(property="rectangle_cover_android", type="string",  example="http://xxx.yyy.zzz/photo.png", description="游戏長形图"),

     *                      @SWG\Property(property="round_cover", type="string",  example="http://xxx.yyy.zzz/photo.png", description="游戏圓角封面图")
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
    public function index()
    {
        try {
            $coverColumn = (config('app.current_platform') == 'ios') ? 'cover_ios' : 'cover_android';
            $list = $this->service->allGames(['name', 'slug', 'cover', 'cover_ios', 'cover_android', 'rectangle_cover_ios', 'rectangle_cover_android', 'round_cover', 'game_app_id'])->map(function ($game) use ($coverColumn) {
                $game->cover = $game->$coverColumn;
                return $game;
            });
            return response()->success($list, 200);
        } catch (\Exception $ex) {
            return response()->error();
        }
    }
}
