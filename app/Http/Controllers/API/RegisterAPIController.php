<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Code;
use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Register\RegisterRequestRule;
use App\Http\Requests\Register\SmsSendRequestRule;
use App\Http\Requests\Register\SmsValidateRequestRule;
use App\Services\ChannelService;
use App\Services\SmsService;
use App\Services\UserService;
use Exception;

class RegisterAPIController extends Controller
{
    private $userService;
    private $smsService;
    private $channelService;

    public function __construct(UserService $userService, SmsService $smsService, ChannelService $channelService)
    {
        $this->userService = $userService;
        $this->smsService = $smsService;
        $this->channelService = $channelService;
    }

    /**
     * @SWG\Post(
     *     path="/user/register",
     *     summary="註冊 API",
     *     description="用戶註冊",
     *     operationId="/user/register",
     *     tags={"註冊"},
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
     *         description="密碼, 8-16個英數字",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sms_code",
     *         in="formData",
     *         description="簡訊驗證碼, 6個數字",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="channel",
     *         in="formData",
     *         description="渠道號, 測試用請帶test",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="uuid",
     *         in="formData",
     *         description="手機裝置識別碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="formData",
     *         description="手機型號",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="os_version",
     *         in="formData",
     *         description="作業系統版本, ios or android",
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
     *                 property="message",
     *                 type="string"
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="id", type="integer", example=12000001),
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
    public function register(RegisterRequestRule $request)
    {
        // 需要做的驗證
        // 1. 手機號碼, 密碼, 驗證碼的格式
        // 2. 是否已註冊
        // 3. 驗證碼是否正確
        try {
            $parameters = $request->all();
            \DB::beginTransaction();

            // 檢查手機號碼是否已經註冊
            if ($this->userService->checkExistByCellphone($parameters['cellphone']) == true) {
                throw new \Exception(__('message.cellphone_already_used'));
            }
            // 檢查驗證碼是否正確
            if ($this->smsService->checkSmsCode($parameters['cellphone'], $parameters['sms_code']) == false) {
                throw new \Exception(__('message.sms_code_validation_fail'), Code::SMS_VALID_CODE_EXPIRED);
            }

            // 檢查渠道號是否正確
            if ($this->channelService->checkKeyIsValid($parameters['channel']) == false) {
                throw new \Exception(__('message.invalid_channel_key'));
            }

            // 產生 user 資料
            $data = $this->userService->create($request->all());

            // 回傳格式

            $response = ['id' => $data->id];
            \DB::commit();

            // 返回成功結果
            return response()->success($response, 200);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/register/sms/validate",
     *     summary="驗證簡訊驗證碼API",
     *     description="驗證簡訊驗證碼API",
     *     operationId="smsValidate",
     *     tags={"註冊"},
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="用戶的手機, 11個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="sms_code",
     *         in="formData",
     *         description="簡訊驗證碼, 6個數字",
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
    public function smsValidate(SmsValidateRequestRule $request)
    {
        try {
            // 取得request的參數
            $parameters = $request->all();

            // 檢查驗證碼是否正確
            if ($this->smsService->checkSmsCode($parameters['cellphone'], $parameters['sms_code']) == false) {
                throw new \Exception(__('message.sms_code_validation_fail'));
            }

            // 返回成功結果
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/register/sms/send",
     *     summary="傳送註冊簡訊 API",
     *     description="將註冊驗證碼傳到用戶手機",
     *     operationId="/user/register/sms/send",
     *     tags={"註冊"},
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="用戶的手機, 11個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="is_resend",
     *         in="formData",
     *         description="是否為重送, 0:否, 1:是, 預設為0",
     *         required=false,
     *         type="integer"
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
    public function sms(SmsSendRequestRule $request)
    {
        // 需要做的驗證
        // 1. 手機號碼格式是否合格
        // 2. 手機號碼是否已註冊 (是否存在User資料表)
        try {
            // 產生驗證碼
            $code = $this->smsService->generateCode();

            // 取得request的參數
            $parameters = $request->all();
            $parameters['code'] = $code;
            $parameters['nationcode'] = $parameters['nationcode'] ?? 86;
            // 檢查手機號碼是否已經註冊
            if ($this->userService->checkExistByCellphone($parameters['cellphone']) == true) {
                throw new \Exception(__('message.cellphone_already_used'));
            }
            $resend = false;
            if (isset($parameters['is_resend']) && $parameters['is_resend'] == 1) {
                $resend = true;
            }

            // 送出簡訊
            $this->smsService->send($parameters, trans('sms.register', ['validation_code' => $code]), $resend);

            // 返回成功結果
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
