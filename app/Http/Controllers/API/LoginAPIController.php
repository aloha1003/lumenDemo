<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Login\LoginCellphoneRequestRule;
use App\Http\Requests\Login\LoginIdRequestRule;
use App\Services\BlockDeviceService;
use App\Services\GameService;
use App\Services\UserLoginRecordService;
use App\Services\UserLoginService;
use App\Services\UserService;

// use Illuminate\Http\Request;

class LoginAPIController extends Controller
{
    private $service;
    private $gameService;
    private $blockDeviceService;
    private $userService;
    private $userLoginRecordService;
    public function __construct(UserLoginService $service, UserService $userService, GameService $gameService, BlockDeviceService $blockDeviceService, UserLoginRecordService $userLoginRecordService)
    {
        $this->service = $service;
        $this->gameService = $gameService;
        $this->blockDeviceService = $blockDeviceService;
        $this->userService = $userService;
        // $this->userLoginRecordService = $userLoginRecordService;
    }

    /**
     * @SWG\Post(
     *     path="/user/id/login",
     *     summary="ID登入 API",
     *     tags={"登入"},
     *     description="用ID來進行登入",
     *     operationId="loginID",
     *     @SWG\Parameter(
     *         name="user_id",
     *         in="formData",
     *         description="用戶id, 8個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="密碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="os_version",
     *         in="formData",
     *         description="系統版本, 可輸入 ios or android",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="uuid",
     *         in="formData",
     *         description="设备当前uuid",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="formData",
     *         description="手机型号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="channel",
     *         in="formData",
     *         description="用户APP安装时的渠道",
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
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="id", type="integer", example=12000001),
     *                      @SWG\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9"),
     *                      @SWG\Property(property="sign", type="string", example="lIjoiVVRDIn0sImV4"),
     *                      @SWG\Property(property="sdkAppId", type="string", example=1234569999),
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
    public function loginID(LoginIdRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $parameters['ip'] = ip();
            $id = $parameters['user_id'];
            $user = $this->userService->findById($id)->first();
            if ($user == null) {
                throw new \Exception(trans("response.code.100003"), 100003);
            }
            // 驗證資料並回傳 token
            $result = $this->doLogin($user, $parameters);
            \DB::commit();
            return $result;
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->error($e);
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/cellphone/login",
     *     summary="手機登入 API",
     *     description="用手機來進行登入",
     *     tags={"登入"},
     *     operationId="user/cellphone/login",
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="用戶的手機, 11個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="密碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="os_version",
     *         in="formData",
     *         description="系統版本, 可輸入 ios or android",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="uuid",
     *         in="formData",
     *         description="设备当前uuid",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="formData",
     *         description="手机型号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="channel",
     *         in="formData",
     *         description="用户APP安装时的渠道",
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
     *                 property="message",
     *                 type="string"
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="id", type="integer", example=12000001),
     *                      @SWG\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9"),
     *                      @SWG\Property(property="sign", type="string", example="lIjoiVVRDIn0sImV4"),
     *                      @SWG\Property(property="sdkAppId", type="string", example=1234569999),
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
    public function loginCellphone(LoginCellphoneRequestRule $request)
    {
        try {

            \DB::beginTransaction();

            $parameters = $request->all();
            $parameters['ip'] = ip();
            // dd($parameters);
            $cellphone = $parameters['cellphone'];
            $user = $this->userService->findByCellphone($cellphone)->first();
            if ($user == null) {
                throw new \Exception(trans("response.code.100003"), 100003);
            }

            $result = $this->doLogin($user, $parameters);
            \DB::commit();
            return $result;
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->error($e);
        }
    }

    /**
     * 做登录的工作，和相关的记录资料修改
     *
     * @param    [type]                   $auth       [description]
     * @param    [type]                   $user       [description]
     * @param    [type]                   $parameters [description]
     *
     * @return   [type]                               [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-25T10:54:39+0800
     */
    private function doLogin($user, $parameters)
    {
        // 驗證資料並回傳 token
        // 登入資料準備
        $auth = [
            'id' => $user->id,
            'password' => $parameters['password'],
        ];
        //检查是否有被封设备号
        if ($this->blockDeviceService->isNeedBlockByUUID($parameters['uuid'])) {
            throw new \Exception(__('response.code.100001'), 100004);
        }
        if (\JWTAuth::validID($auth)) {
            // IM登入 的 sign

            $sign = \Live::userSig($user->id, true);
            // 修改相关的记录
            $parameters['user_id'] = $user->id;
            $this->service->pushUpdateRelatedRecordJob($user, $parameters);
            //产生jwt token
            $token = \JWTAuth::generate('', ['os' => $parameters['os_version']]);
            /*** 游戏登入 ***/
            // 单纯游戏后台做统计用
            $resversion = GameService::RES_VERSION; // 1.2
            $channel = GameService::CHANNEL; // 1000
            $regGame = GameService::REG_GAME; //100
            $devId = $user->id;
            $osver = $parameters['os_version'];
            $lineNo = $user->cellphone; // 手機號碼

            $this->gameService->quickRegisterAndLogin($user->id, $user->nickname, $token, $resversion, $channel, $regGame, $devId, $lineNo, $osver, $osver, $lineNo);
            /*** 游戏登入 结束***/
            return response()->success(
                [
                    'id' => $user->id,
                    'token' => $token,
                    'sign' => $sign,
                    'sdkAppId' => getTencentLiveConfigFromCache()['system']['play_app_id'],
                ]
            );
        } else {
            throw new \Exception(trans("response.code.100003"), 100003);
        }
    }
    /**
     * 產生 user 簽名
     *
     * @return void
     */
    /**
     * @SWG\Post(
     *     path="/user/sdk/signature",
     *     summary="取得第三方签名",
     *     description="取得第三方签名",
     *     tags={"登入"},
     *     operationId="signature",
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
     *               @SWG\Property(property="data", type="object",
    @SWG\Property(property="sign", type="string"),
    @SWG\Property(property="sdkAppId", type="string"),
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
    public function signature()
    {
        try {
            $sign = \Live::userSig(id(), true);
            $tencentLiveConfig = getTencentLiveConfigFromCache();

            $output = [
                'sign' => $sign,
                'sdkAppId' => $tencentLiveConfig['system']['play_app_id'],
            ];
            return response()->success($output, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }
}
