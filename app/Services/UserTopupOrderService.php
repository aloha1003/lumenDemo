<?php
namespace App\Services;

use App\Models\AnnouceForUser as AnnouceForUserModel;
use App\Models\UserTopupAppeal as UserTopupAppealModel;
use App\Models\UserTopupOrder as UserTopupOrderModel;
use App\Repositories\Interfaces\AnnouceForUserRepository;
use App\Repositories\Interfaces\PayChannelPaymentRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\UserTopupAppealRepository;
use App\Repositories\Interfaces\UserTopupOrderRepository;
use App\Services\UserLevelService;
use Carbon\Carbon;

//充值服务
class UserTopupOrderService
{
    use \App\Traits\MagicGetTrait;
    private $payChannelPaymentRepository;
    private $repository;
    private $userTopupAppealRepository;
    private $userRepository;
    private $annouceForUserRepository;
    //申诉图片路径
    const DEFAULT_PHOTO_FILENAME_PREFIX = 'appeal';
    const DEFAULT_PHOTO_ROOT_DIR = 'appeal/';

    const TOPUP_LINK_PREFIX = 'topup_link:';
    public function __construct(
        UserTopupOrderRepository $repository,
        UserRepository $userRepository,
        UserTopupAppealRepository $userTopupAppealRepository,
        AnnouceForUserRepository $annouceForUserRepository,
        PayChannelPaymentRepository $payChannelPaymentRepository) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->payChannelPaymentRepository = $payChannelPaymentRepository;
        $this->annouceForUserRepository = $annouceForUserRepository;

        $this->userTopupAppealRepository = $userTopupAppealRepository;
    }

    /**
     * 新增一笔交易
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-10T16:03:27+0800
     */
    public function topup($data, $channel = 'test')
    {
        //验证交易方式是否合法
        $payPayment = $this->payChannelPaymentRepository->find($data['pay_id']);
        $userTopupOrderModel = $this->repository->makeModel();
        $this->payChannelPaymentRepository->isCanPay($payPayment, $data);
        $topupData = [
            'transaction_no' => $this->repository->generateTransactionNo($data['user_id']),
            'pay_step' => $userTopupOrderModel::PAY_STEP_INIT,
            'user_id' => $data['user_id'],
            'pay_channels_slug' => $payPayment->pay_channels_slug,
            'pay_channel_payments_pay_type' => $payPayment->pay_type,
            'fee' => $payPayment->fee,
            'amount' => $data['amount'],
            'pay_detail' => json_encode($payPayment),
            'user_register_channel' => $channel,
        ];
        $order = $this->repository->create($topupData);
        if (config('app.env') != 'production') {
            //只有非正式环境才要做，10秒会自动做
            \Queue::laterOn('default', 10, new \App\Jobs\AutoManualTopupNotifyForDevelop($topupData['transaction_no']));
        }
        return $order;
    }

    /**
     * 做第三方交易
     *
     * @param    [type]                   $order [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-17T11:24:30+0800
     */
    public function doThirdPay($order)
    {
        $thirdPay = payment($order->pay_channels_slug, $order->pay_channel_payments_pay_type);
        $result = $thirdPay->setOrderData($order)->pay($order->toArray());
        return $result;
    }

    /**
     * 格式化输出充值结果
     *
     * @param    [type]                   $order [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-17T11:25:27+0800
     */
    public function formatTopupResult($order)
    {
        $formatResponse = ['id' => $order->id, 'transaction_no' => $order->transaction_no, 'link' => $order->payUrl, 'call_back_type' => $order->payload['callBackType']];
        return $formatResponse;
    }

    public function save($id, $data)
    {
        try {
            $item = $this->repository->find($id);
            $return = $item->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 所有资料
     *
     * @param    array                    $columns 要选择的栏位，预设全部
     *
     * @return   collect                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T16:03:43+0800
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * 完成订单交易
     *
     * @param    [type]                   $transactionNo [description]
     * @param    [type]                   $updateData    [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-20T11:22:17+0800
     */
    public function finishTransaction($transactionNo, $updateData, $payload)
    {

        $userTopupOrderModel = $this->repository->makeModel();
        $order = $this->repository->makeModel()->where('transaction_no', '=', $transactionNo)->lockForUpdate()->first();
        if (!$order) {
            throw new \Exception(__('common.not_found_records'));
        }
        //检查金额有没有符合
        if ($updateData['amount']) {
            if ($order->amount != $updateData['amount']) {
                throw new \Exception(__('userTopupOrder.amount_mismatch'));
            }
        }
        //检查成本
        if (!(isset($updateData['cost']) && $updateData['cost'])) {
            //手动计算手续费
            $payPayment = $this->payChannelPaymentRepository->findWhere(['pay_channels_slug' => $order->pay_channels_slug, 'pay_type' => $order->pay_channel_payments_pay_type]);
            if ($payPayment->count() == 0) {
                throw new \Exception(__('common.not_valid_payment'));
            }
            $payPayment = $payPayment->first();
            $updateData['cost'] = $this->payChannelPaymentRepository->getCost($payPayment, $order->amount);
        }
        //计算净额
        $updateData['profit'] = $order->amount - $order['cost'];
        $order->pay_step = $userTopupOrderModel::PAY_STEP_SUCCESS;
        $order->pay_at = date("Y-m-d H:i:s", time());
        $order->notify_status = $userTopupOrderModel::NOTIFY_STATUS_PASS;
        $order->payload = $payload;
        foreach ($updateData as $key => $value) {
            $order->$key = $value;
        }
        $order->save();

        //增加經驗
        app(UserLevelService::class)->addExpByTopupRMB($order->user_id, $order->amount);
    }

    /**
     * 更新交易状态
     *
     * @param    [type]                   $transactionNo [description]
     * @param    [type]                   $updateData    [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-20T11:22:17+0800
     */
    public function updateStatusByID($id, $updateData)
    {
        $order = $this->repository->findWhere($id);
        $order->update($updateData);
    }

    /**
     * 完成订单交易
     *
     * @param    [type]                   $transactionNo [description]
     * @param    [type]                   $updateData    [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-20T11:22:17+0800
     */
    public function updateTransactionByTransactionNo($transactionNo, $updateData, $payload)
    {
        try {
            $userTopupOrderModel = $this->repository->makeModel();
            $order = $this->repository->findWhere(['transaction_no' => $transactionNo]);
            if ($order->count() == 0) {
                throw new \Exception(__('common.not_found_records'));
            }
            $order = $order->first();
            $order->payload = $payload;
            if ($order->notify_status == $userTopupOrderModel::NOTIFY_STATUS_PASS) {
                unset($updateData['notify_status']);
            }
            foreach ($updateData as $key => $value) {
                $order->$key = $value;
            }
            $order->save();
        } catch (\Exception $ex) {
            $order->payload = $ex;
            $order->save();
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 根据过滤条件取得资料
     *
     * @param    array                    $columns 要选择的栏位，预设全部
     *
     * @return   collect                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T16:03:43+0800
     */
    public function obtainData($filter = [], $offset = 1, $columns = ['*'])
    {
        $orderByColumn = 'created_at';
        $offset = ($offset - 1) * config('app.api_per_page_data');
        $model = app($this->repository->model());
        $between = [];
        $where = $filter;
        if (isset($filter['pay_at_start']) && ($filter['pay_at_start'])) {
            $between[] = ['pay_at', '>=', $filter['pay_at_start']];
        }
        if (isset($filter['pay_at_end']) && ($filter['pay_at_end'])) {
            $between[] = ['pay_at', '<=', $filter['pay_at_end']];
        }
        unset($where['pay_at_start']);
        unset($where['pay_at_end']);
        $formatWhere = [];
        foreach ($where as $key => $val) {
            if ($val) {
                $formatWhere[] = [$key, '=', $val];
            }
        }
        if ($between) {
            $formatWhere = array_merge($formatWhere, $between);
        }
        $userTopupOrderModel = $this->repository->makeModel();
        $formatWhere[] = ['pay_step', '!=', $userTopupOrderModel::PAY_STEP_THIRD_ERR];
        $result = $this->repository->injectSearchAutoIndex($formatWhere)->paginate();
        return $result->items();
    }

    /**
     * 透过订单编号取得资料
     *
     * @param    [type]                   $no [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T10:20:31+0800
     */
    public function obtainOrder($where)
    {
        $order = $this->repository->findWhere($where);
        if ($order->count() == 0) {
            throw new \Exception(__('common.not_found_records'));
        }
        return $order->first()->toArray();
    }

    /**
     * 手动确认第三方订单是否成功,成功的话，去上分
     *
     * @param    string                   $transactionNo [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T11:14:13+0800
     */
    public function manualSendNotify($transactionNo)
    {
        $order = $this->repository->findWhere(['transaction_no' => $transactionNo]);
        if ($order->count() == 0) {
            throw new \Exception(__('common.not_found_records'));
        }
        $userTopupOrderModel = $this->repository->makeModel();
        $order = $order->first();
        if ($order->pay_step == $userTopupOrderModel::PAY_STEP_SUCCESS) {
            throw new \Exception(__('userTopupOrder.has_finish_this_order'));
        }
        $channelInstance = channel($order->pay_channels_slug);
        if ($channelInstance->isOrderFinish($order)) {
            $channelOrder = $channelInstance->queryOrder($order);
            $updateData = [
                'cost' => $channelOrder['cost'] ?? '', //有的支付并不一定回调会回传手续费
                'amount' => $channelOrder['amount'] ?? '', //当前付款金额
            ];
            $this->finishTransaction($transactionNo, $updateData, []);
        } else {
            throw new \Exception(__('userTopupOrder.order_not_yet_finish'));
        }
    }

    /**
     * 充值訂單申訴審核紀錄寫入
     */
    public function appealReviewSave($appealId, $title, $content, $review, $opAdminId)
    {
        $appealModel = $this->userTopupAppealRepository->findWhere(['id' => $appealId])->first();

        if ($review == UserTopupAppealModel::STATUS_SUCCESS) {
            $appealModel->status = UserTopupAppealModel::STATUS_SUCCESS;
        } else {
            $appealModel->status = UserTopupAppealModel::STATUS_FAIL;
        }
        $appealModel->save();

        $annouceData = [
            'user_id' => $appealModel->user_id,
            'type_slug' => AnnouceForUserModel::DEFAULT_TYPE_SLUG,
            'title' => $title,
            'content' => $content,
            'admin_id' => $opAdminId,
        ];
        $this->annouceForUserRepository->create($annouceData);

        // 用im傳送通知
        $broadCastData = [
            "SyncOtherMachine" => 2, // 消息不同步至发送方
            "To_Account" => (string) $appealModel->user_id,
            'MsgBody' => [
                [
                    'MsgType' => 'TIMCustomElem',
                ],
            ],
        ];
        $result = \IM::sendSingleUser($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.106', ['announceData' => 1])]]);
    }

    /**
     * 訂單申訴
     */
    public function setAppeal($userId, $orderId, $contactInfo, $detailInfo, $photoUrl)
    {
        $orderModel = $this->repository->findWhere(['id' => $orderId])->first();
        // 訂單號不存在
        if ($orderModel == null) {
            throw new \Exception(__('userTopupOrder.order_not_exist'));
        }

        // 訂單號與用戶id不同步
        if ($orderModel->user_id != $userId) {
            throw new \Exception(__('userTopupOrder.not_your_order'));
        }

        // 訂單已提出申訴
        if ($orderModel->appeal_status != UserTopupOrderModel::APPEAL_STATUS_CAN) {
            throw new \Exception(__('userTopupOrder.order_already_set_appeal'));
        }

        // 更新order的appeal狀態
        $orderModel->appeal_status = UserTopupOrderModel::APPEAL_STATUS_WAIT;
        $orderModel->save();

        // 寫入申訴資料
        $transactionNo = $orderModel->transaction_no;
        $data = [
            'status' => UserTopupAppealModel::STATUS_WAIT,
            'user_id' => $userId,
            'order_id' => $orderId,
            'transaction_no' => $transactionNo,
            'contact_info' => $contactInfo,
            'detail_info' => $detailInfo,
            'photo_url' => $photoUrl,
        ];
        $this->userTopupAppealRepository->create($data);
    }

    /**
     * 取得上傳申訴圖片的path與token
     */
    public function getUploadPhotoTokenAndFilePath($userId, $orderId)
    {
        // 用user_id找出相對應的用戶
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }

        // 依照用戶user id & order id, 組成上傳檔案的路徑
        $fullPathName = $this->getPhotoUploadFilePath($userId, $orderId);

        // 取得token
        $token = \CLStorage::getDriver()->uploadToken($fullPathName, 3600, ['insertOnly' => 0], true);

        $result = [
            'token' => $token,
            'file_path' => $fullPathName,
        ];
        return $result;
    }

    /**
     * 取得申訴圖檔上傳到雲端的存放路徑
     */
    protected function getPhotoUploadFilePath($userId, $orderId)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_PHOTO_FILENAME_PREFIX . $userId . $orderId . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_PHOTO_ROOT_DIR . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 返回充值真实连结
     *
     * @param    [type]                   $orderNo [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-26T09:47:29+0800
     */
    public static function getLinkCacheKey($orderNo)
    {
        return self::TOPUP_LINK_PREFIX . $orderNo;
    }

}
