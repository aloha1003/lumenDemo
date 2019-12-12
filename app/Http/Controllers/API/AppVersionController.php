<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\AppVersion\AppVersionRequestRule;
use App\Services\AppReleaseService;
use App\Services\GameReleaseService;

class AppVersionController extends Controller
{
    private $appReleaseService;
    private $gameReleaseService;

    public function __construct(AppReleaseService $appReleaseService, GameReleaseService $gameReleaseService)
    {
        $this->appReleaseService = $appReleaseService;
        $this->gameReleaseService = $gameReleaseService;

    }

    /**
     * @SWG\Post(
     *     path="/app/version",
     *     summary="app資訊",
     *     description="游戏列表",
     *     operationId="getAll",
     *     tags={"系統"},
     *     @SWG\Parameter(
     *         name="channel_slug",
     *         in="formData",
     *         description="渠道識別碼",
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
     *               @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(property="official_url", type="string",example="http://www.jusi888.com:5082", description="官方連結"),
     *                 @SWG\Property(property="ios", type="object",
     *                          @SWG\Property(property="ios_version_code", type="string", example="1.0.0", description="版本號"),
     *                          @SWG\Property(property="ios_version_number", type="string", example="1.0.0", description="版本號"),
     *                          @SWG\Property(property="download_url", type="string", example="http://xxx.yyy.zzz/xxx.ipa" , description="下載連結"),
     *                          @SWG\Property(property="cdn_download_url", type="string", example="http://www.cdn.com/xxx.ipa" , description="cdn下載連結")
     *                 ),
     *                 @SWG\Property(property="android", type="object",
     *                          @SWG\Property(property="android_version_code", type="string", example="1.0.0", description="版本號"),
     *                          @SWG\Property(property="android_version_number", type="string", example="1.0.0", description="版本號"),
     *                          @SWG\Property(property="download_url", type="string", example="http://xxx.yyy.zzz/xxx.apk" , description="下載連結"),
     *                          @SWG\Property(property="cdn_download_url", type="string", example="http://www.cdn.com/xxx.apk" , description="cdn下載連結")
     *                 ),
     *                 @SWG\Property(property="game", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="game_slug", type="string", example="132", description="遊戲識別碼"),
     *                          @SWG\Property(property="version", type="string", example="1.0.0", description="版本號"),
     *                          @SWG\Property(property="download_url", type="string", example="http://xxx.yyy.zzz/xxx.apk" , description="下載連結"),
     *                          @SWG\Property(property="cdn_download_url", type="string", example="http://www.cdn.com/xxx.apk" , description="cdn下載連結")
     *                      )
     *                 )
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
    public function getAll(AppVersionRequestRule $request)
    {
        $parameters = $request->all();
        $appData = $this->appReleaseService->getAllRelease($parameters['channel_slug']);
        $gameData = $this->gameReleaseService->getAllRelease();

        if ($appData['ios'] == []) {
            $appData['ios'] = null;
        }
        if ($appData['android'] == []) {
            $appData['android'] = null;
        }
        $result = [
            'official_url' => $appData['official_url'],

            'ios' => $appData['ios'],
            'android' => $appData['android'],
            'game' => $gameData['game'],
        ];
        return response()->success($result);
    }
}
