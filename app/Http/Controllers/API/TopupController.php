<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\API\Topup\AppealPhotoTokenRequest;
use App\Http\Requests\API\Topup\AppealRequest;
use App\Http\Requests\API\Topup\PayRequest;
use App\Http\Requests\API\Topup\QueryRequest;
use App\Services\PayChannelPaymentService;
use App\Services\UserTopupOrderService;

class TopupController extends Controller
{
    private $service;
    public function __construct(PayChannelPaymentService $payChannelPaymentService, UserTopupOrderService $userTopupOrderService)
    {
        $this->payChannelPaymentService = $payChannelPaymentService;
        $this->userTopupOrderService = $userTopupOrderService;
    }

    /**
     * @SWG\Post(
     *     path="/topup/pay_list",
     *     summary="交易方式列表",
     *     description="交易方式列表",
     *     operationId="pay_list",
     *     tags={"充值"},
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
     *               @SWG\Property(
     *                 property="data",
     *                 type="array",
    type="array",
     *                  @SWG\Items(
     *                      type="object",
    ref="#/definitions/储值方式"
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
    /**
     * 支付方式列表
     *
     * @return   array                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T10:39:29+0800
     */
    public function paylist()
    {
        try {
            $columns = ['id', 'alias', 'order_amounts', 'custom_amount', 'pay_type', 'pay_channels_slug'];
            $payList = $this->payChannelPaymentService->allAvaialbePayments($columns);
            return response()->success($payList, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/topup/pay",
     *     summary="创建充值请求",
     *     description="创建充值请求",
     *     operationId="pay",
     *     tags={"充值"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="pay_id",
     *         in="formData",
     *         description="交易方式id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="formData",
     *         description="充值金额",
     *         required=true,
     *         type="number"
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
     *                 type="array",
    type="array",
     *                  @SWG\Items(
     *                      type="object",
    ref="#/definitions/充值返回资料"
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
    public function pay(PayRequest $request)
    {
        //先建立交易单
        try {
            \DB::beginTransaction();
            $userTopupOrderModel = $this->userTopupOrderService->getRepository()->makeModel();
            $data = $request->only('amount', 'pay_id');
            $data['user_id'] = id();
            $currentUser = whoami();
            if (!$currentUser) {
                $channel = 'test';
            } else {
                $channel = whoami()->register_channel;
            }
            $order = $this->userTopupOrderService->topup($data, $channel);
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
        //做第三方交易
        try {
            \DB::beginTransaction();
            $thirdPayResult = $this->userTopupOrderService->doThirdPay($order);
            $result = $thirdPayResult->formatResult();
            $order->pay_transaction_no = $result['payTransactionNo'];
            $order->pay_step = $userTopupOrderModel::PAY_STEP_PEND;
            $order->payUrl = $result['link'];
            $order->payload = $result;
            $order->save();
            \DB::commit();
            $result = $this->userTopupOrderService->formatTopupResult($order);
            return response()->success($result, 200);
        } catch (\Exception $ex) {
            //第三方交易失败
            \DB::rollback();
            try {
                \DB::beginTransaction();
                $order->pay_step = $userTopupOrderModel::PAY_STEP_THIRD_ERR;
                $order->payload = formatException($ex, false, true);
                $order->save();
                \DB::commit();
            } catch (\Exception $ex) {
                \DB::rollback();
                return response()->error($ex);
            }
            return response()->error($ex);
        }
    }
    /**
     *
     * @SWG\Definition(
     *      definition="订单查询条件",
     *     @SWG\Property(
     *         property="pay_at_start",
     *         description="交易完成日期-起始日期",
     *         type="date"
     *     ),
     *     @SWG\Property(
     *         property="pay_at_end",
     *         description="交易完成日期-结束日期",
     *         type="date"
     *     ),
     *     @SWG\Property(
     *         property="pay_type",
     *         description="交易方式 ALI:支付宝,WEIXIN:微信,IBK:网银,UNION:銀聯,QQ:QQ钱包,CLD:云闪付 ",
     *         enum={"ALI","WEIXIN","IBK","UNION","QQ","CLD"},
     *         type="integer"
     *     ),
     *     @SWG\Property(
     *         property="pay_step",
     *         description="交易状态 INIT:初始化, THIRD_ERR:第三方支付失败, PEND: 交易递延中，SUCCESS:交易成功，ABORT:交易失败, CANCEL:無效訂單",
     *         enum={"INIT","THIRD_ERR","PEND","SUCCESS","ABORT","CANCEL"},
     *         type="string"
     *     )
     *   )
     */
    /**
     * @SWG\Post(
     *     path="/topup/record_list",
     *     summary="交易记录列表",
     *     description="交易记录列表",
     *     operationId="record_list",

     *     tags={"充值"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="分页数字",
     *         required=false,
     *         default=1,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="filter_data",
     *         in="body",
     *         description="过滤条件,实际程式在呼叫的时候，可以用form表单同名的变数传送",
     *         required=true,
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/订单查询条件")
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
     *                 type="array",
    type="array",
     *                  @SWG\Items(
     *                      type="object",
    ref="#/definitions/充值订单资料"
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
    public function recordList(QueryRequest $request)
    {
        try {
            $data = $request->only('page', 'filter_data');
            $filter = $data['filter_data'] ?? $request->getContent();
            if ($filter) {
                $filter = json_decode($filter, true);
                if (json_last_error()) {
                    $filter = [];
                }
            }
            if (isset($filter['pay_type'])) {
                $filter['pay_channel_payments_pay_type'] = $filter['pay_type'];
                unset($filter['pay_type']);
            }
            $filter['user_id'] = id();
            $page = $data['page'] ?? 1;
            $result = $this->userTopupOrderService->obtainData($filter, $page);
            $viewColumns = ['id', 'transaction_no', 'pay_at', 'created_at', 'gold', 'amount', 'pay_type', 'pay_step_title', 'appeal_status'];
            $result = collect($result)->map(function ($item) use ($viewColumns) {
                $item['pay_type'] = $item['pay_channel_payments_pay_type'] ?? '';
                $return = [];
                foreach ($viewColumns as $key => $value) {
                    $return[$value] = $item[$value];
                }
                return $return;
            })->toArray();
            return response()->success($result, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/topup/record/{transaction_no}",
     *     summary="交易记录",
     *     description="交易记录",
     *     operationId="record",

     *     tags={"充值"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="transaction_no",
     *         in="path",
     *         description="订单编号",
     *         required=true,
     *         type="string",
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
     *                 type="array",
    type="array",
     *                  @SWG\Items(
     *                      type="object",
    ref="#/definitions/充值订单资料"
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
    public function record($transactionNo)
    {
        try {
            $filter = [
                'transaction_no' => $transactionNo,
                'user_id' => id(),
            ];
            $result = $this->userTopupOrderService->obtainOrder($filter);

            $viewColumns = ['id', 'transaction_no', 'pay_at', 'created_at', 'gold', 'amount', 'pay_type', 'pay_step_title', 'appeal_status'];
            $result['pay_type'] = $result['pay_channel_payments_pay_type'] ?? '';
            $return = [];
            foreach ($viewColumns as $key => $value) {
                $return[$value] = $result[$value];
            }
            return response()->success($return, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/topup/appeal/set",
     *     summary="充值訂單申訴api",
     *     tags={"充值"},
     *     description="充值訂單申訴api",
     *     operationId="appeal",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="order_id",
     *         in="formData",
     *         description="充值訂單的流水號",
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
    public function appeal(AppealRequest $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $userId = id();
            $orderId = $parameters['order_id'];
            $contactInfo = $parameters['contact_info'];
            $detailInfo = $parameters['detail_info'];
            $photoUrl = '';
            if (isset($parameters['photo_url'])) {
                $photoUrl = $parameters['photo_url'];
            }
            $this->userTopupOrderService->setAppeal($userId, $orderId, $contactInfo, $detailInfo, $photoUrl);
            \DB::commit();
            return response()->success(['status' => true], 200);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/topup/appeal/photo/token",
     *     tags={"充值"},
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
     *         name="order_id",
     *         in="formData",
     *         description="充值訂單的流水號",
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
    public function getAppealPhotoToken(AppealPhotoTokenRequest $request)
    {
        try {
            $parameters = $request->all();
            $orderId = $parameters['order_id'];
            $id = id();
            $result = $this->userTopupOrderService->getUploadPhotoTokenAndFilePath($id, $orderId);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

}
