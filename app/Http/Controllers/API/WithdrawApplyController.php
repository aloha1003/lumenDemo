<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\WithDrawApply\AddBankRequestRule;
<<<<<<< HEAD
=======
use App\Http\Requests\API\WithDrawApply\RemoveBankRequestRule;

>>>>>>> develop
use App\Http\Requests\API\WithDrawApply\Request;
use App\Http\Requests\API\WithDrawApply\UpdateBankRequestRule;
use App\Http\Requests\API\WithDrawApply\WithdrawAppealPhotoTokenRequestRule;
use App\Http\Requests\API\WithDrawApply\WithdrawAppealRequestRule;
<<<<<<< HEAD
use App\Services\WithDrawGoldApplyService;
=======
use App\Http\Requests\API\WithDrawApply\AddBankForAliRequestRule;
use App\Http\Requests\API\WithDrawApply\SetBankAsUsualRequestRule;
use App\Services\WithDrawGoldApplyService;
use App\Models\PaymentChannel as PaymentChannelModel;
use App\Exceptions\Code;
>>>>>>> develop

// 用戶提現
class WithdrawApplyController extends Controller
{
    private $service;
    public function __construct(WithDrawGoldApplyService $service)
    {
        $this->service = $service;
    }
    /**
     * @SWG\Post(
     *     path="/withdraw/apply",
     *     summary="提现",
     *     description="提现申请",
     *     operationId="apply",
     *     tags={"提现"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="rmb",
     *         in="formData",
     *         description="人民币",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="payment_channels_slug",
     *         in="formData",
     *         description="帐户类型",
     *         required=true,
     *         enum={"ali"},
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="account",
     *         in="formData",
     *         description="转出帐号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="account_confirm",
     *         in="formData",
     *         description="确认转出帐号",
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
    public function apply(Request $request)
    {
        try {
            \DB::beginTransaction();
            $input = $request->only(['rmb', 'account', 'account_confirm', 'payment_channels_slug']);
            $input['user_id'] = id();
            $coinRatio = sc('coinRatio');

            $input['gold'] = $input['rmb'] * $coinRatio;
            $this->service->sendApply($input);
            \DB::commit();
            return response()->success([], 200);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Get(
     *     path="/withdraw",
     *     summary="列出提现记录",
     *     description="列出提现记录",
     *     operationId="list",
     *     tags={"提现"},
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
    ref="#/definitions/提现记录"
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
            $result = $this->service->all(['user_id' => id()]);
            return response()->success($result, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/user/gold/info",
     *     summary="用戶提现的金幣資訊",
     *     description="用戶提现的金幣資訊",
     *     operationId="userGoldInfo",
     *     tags={"提现"},
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
     *                      @SWG\Property(property="gold", type="string", example=957495811.00),
     *                      @SWG\Property(property="rmb", type="string", example=957495811.00),
     *                      @SWG\Property(property="can_withdraw_times", type="string", example=3),
     *                      @SWG\Property(property="can_withdraw_rmb", type="string", example=300000),
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
    public function userGoldInfo()
    {
        try {
            \DB::beginTransaction();
            $userId = id();
            $result = $this->service->getGoldInfo($userId);
            \DB::commit();
            return response()->success($result, 200);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/add/bank/info",
     *     summary="用戶增加提現帳戶資訊",
     *     description="用戶增加提現帳戶資訊",
     *     operationId="addBankInfo",
     *     tags={"提现"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="payment_channels_slug",
     *         in="formData",
     *         description="帐户类型, ali: 支付寶, bank_card:銀行卡",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="account",
     *         in="formData",
     *         description="转出帐戶",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="bank_slug",
     *         in="formData",
     *         description="銀行識別碼, 當payment_channels_slug為bank_card時必填",
     *         required=false,
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
    public function addBankInfo(AddBankRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            if ($parameters['payment_channels_slug'] == PaymentChannelModel::BANK_CARD_CHANNEL_SLUG && !isset($parameters['bank_slug'])) {
                throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
            }
            if (!isset($parameters['bank_slug'])) {
                $parameters['bank_slug'] = '';
            }
            $userId = id();
            $this->service->addBankInfo(
                $userId,
                $parameters['payment_channels_slug'],
                $parameters['account'],
<<<<<<< HEAD
                $parameters['name'],
                $parameters['other_info']
=======
                $parameters['bank_slug']
>>>>>>> develop
            );
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/remove/bank/info",
     *     tags={"提现"},
     *     summary="移除銀行資訊",
     *     description="移除銀行資訊",
     *     operationId="removeBankInfo",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="bank_info_id",
     *         in="formData",
     *         description="銀行資訊id",
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
    public function removeBankInfo(RemoveBankRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $userId = id();
            $this->service->removeBankInfo(
                $userId,
                $parameters['bank_info_id']
            );
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/set/bank/info/usual",
     *     tags={"提现"},
     *     summary="設定銀行資訊為常用",
     *     description="設定銀行資訊為常用",
     *     operationId="setBankAsUsual",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="bank_info_id",
     *         in="formData",
     *         description="銀行資訊id",
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
    public function setBankAsUsual(SetBankAsUsualRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $userId = id();
<<<<<<< HEAD
            if (!isset($parameters['other_info'])) {
                $parameters['other_info'] = null;
            }

            $this->service->updateBankInfo(
                $userId,
                $parameters['bank_info_id'],
                $parameters['account'],
                $parameters['name'],
                $parameters['other_info']
=======
            $this->service->setBankInfoToUsual(
                $userId,
                $parameters['bank_info_id']
>>>>>>> develop
            );
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/user/bank/info",
     *     summary="用戶提现帳戶資訊",
     *     description="getBankInfo",
     *     operationId="getBankInfo",
     *     tags={"提现"},
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
     *               @SWG\Property(property="data", type="object",
     *                   @SWG\Property(property="ali", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="bank_info_id", type="string", example=123456),
     *                          @SWG\Property(property="account", type="string", example="asd-asd-asd"),
     *                          @SWG\Property(property="is_usual", type="string", example=1),
     *                          @SWG\Property(property="bank_slug", type="string", example="bank001")
     *                      )
     *                   ),
     *                   @SWG\Property(property="bank_card", type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="bank_info_id", type="string", example=123456),
     *                          @SWG\Property(property="account", type="string", example="asd-asd-asd"),
     *                          @SWG\Property(property="is_usual", type="string", example=1),
     *                          @SWG\Property(property="bank_slug", type="string", example="bank001")
     *                      )
     *                   )
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
    public function getBankInfo()
    {
        try {
            \DB::beginTransaction();
            $userId = id();
            $resutl = $this->service->getBankInfo($userId);
            \DB::commit();
            return response()->success($resutl, 200);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }

    }

    /**
     * @SWG\Post(
     *     path="/withdraw/appeal/set",
     *     summary="提現訂單申訴api",
     *     tags={"提现"},
     *     description="提現訂單申訴api",
     *     operationId="appeal",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="withdraw_id",
     *         in="formData",
     *         description="提現訂單的流水號",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_info",
     *         in="formData",
     *         description="聯絡資訊",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="detail_info",
     *         in="formData",
     *         description="申訴詳細內容描述",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="photo_url",
     *         in="formData",
     *         description="申訴截圖url",
     *         required=false,
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
    public function appeal(WithdrawAppealRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $userId = id();

            $parameters = $request->all();
            $withdrawId = $parameters['withdraw_id'];
            $contactInfo = $parameters['contact_info'];
            $detailInfo = $parameters['detail_info'];
            $photoUrl = $parameters['photo_url'] ?? '';
            $this->service->setAppeal($userId, $withdrawId, $contactInfo, $detailInfo, $photoUrl);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/withdraw/appeal/photo/token",
     *     tags={"提现"},
     *     summary="取申訴圖上傳的token",
     *     description="取申訴圖上傳的token",
     *     operationId="getAppealPhotoToken",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="withdraw_id",
     *         in="formData",
     *         description="提現訂單的流水號",
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
     *                  @SWG\Property(property="token", type="string", example="p-MTIxY_HqyS_f8_qBNK-m3FO7A8VCZwg8WaSWPs:i-w3bntp4LlxL5i"),
     *                  @SWG\Property(property="file_path", type="string", example="appeal/2087-08-07/YXZhdGFyMTIwMDAwMDM="),
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
    public function getAppealPhotoToken(WithdrawAppealPhotoTokenRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $withdrawId = $parameters['withdraw_id'];
            $id = id();
            $result = $this->service->getUploadPhotoTokenAndFilePath($id, $withdrawId);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }

    }
}
