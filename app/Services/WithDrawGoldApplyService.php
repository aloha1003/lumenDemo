<?php
namespace App\Services;

use App\Models\AnnouceForUser as AnnouceForUserModel;
use App\Models\PaymentChannel;
use App\Models\UserDailyWithdrawTimes;
use App\Models\WithdrawAppeal as WithdrawAppealModel;
use App\Models\WithDrawGoldApply;
use App\Repositories\Interfaces\AnnouceForUserRepository;
use App\Repositories\Interfaces\PaymentChannelRepository;
use App\Repositories\Interfaces\RealNameVerifyRepository;
use App\Repositories\Interfaces\UserBankInfoRepository;
use App\Repositories\Interfaces\UserDailyWithdrawTimesRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\WithdrawAppealRepository;
use App\Repositories\Interfaces\WithDrawGoldApplyRepository;
use App\Services\PaymentChannelService;
use Carbon\Carbon;
use App\Exceptions\Code;

//提现服务
class WithDrawGoldApplyService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    private $userRepository;
    private $userBankInfoRepository;
    private $userDailyWithdrawTimesRepository;
    private $paymentChannelRepository;
    private $realNameVerifyRepository;
    private $withdrawAppealRepository;

    const DEFAULT_PHOTO_FILENAME_PREFIX = 'withdraw_appeal';
    const DEFAULT_PHOTO_ROOT_DIR = 'withdraw_appeal/';

    public function __construct(
        WithDrawGoldApplyRepository $repository,
        WithdrawAppealRepository $withdrawAppealRepository,
        UserRepository $userRepository,
        UserBankInfoRepository $userBankInfoRepository,
        UserDailyWithdrawTimesRepository $userDailyWithdrawTimesRepository,
        AnnouceForUserRepository $annouceForUserRepository,
        PaymentChannelRepository $paymentChannelRepository,
        RealNameVerifyRepository $realNameVerifyRepository) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->userBankInfoRepository = $userBankInfoRepository;
        $this->userDailyWithdrawTimesRepository = $userDailyWithdrawTimesRepository;
        $this->paymentChannelRepository = $paymentChannelRepository;
        $this->realNameVerifyRepository = $realNameVerifyRepository;
        $this->withdrawAppealRepository = $withdrawAppealRepository;
        $this->annouceForUserRepository = $annouceForUserRepository;
    }

    /**
     * 對某一提現訂單提出申訴
     */
    public function setAppeal($userId, $withdrawId, $contactInfo, $detailInfo, $photoUrl)
    {
        $withdrawModel = $this->repository->findWhere(['id' => $withdrawId])->first();
        // 提現訂單不存在
        if ($withdrawModel == null) {
            throw new \Exception(__('withDrawGoldApply.withdraw_id_not_exist'));
        }

        // 訂單號與用戶id不同步
        if ($withdrawModel->user_id != $userId) {
            throw new \Exception(__('withDrawGoldApply.not_your_order'));
        }

        // 訂單已提出申訴
        if ($withdrawModel->appeal_status != WithDrawGoldApply::APPEAL_STATUS_CAN) {
            throw new \Exception(__('withDrawGoldApply.withdraw_already_set_appeal'));
        }

        // 更新order的appeal狀態
        $withdrawModel->appeal_status = WithDrawGoldApply::APPEAL_STATUS_WAIT;
        $withdrawModel->save();

        // 寫入申訴資料
        $transactionNo = $withdrawModel->transaction_no;
        $data = [
            'status' => WithdrawAppealModel::STATUS_WAIT,
            'user_id' => $userId,
            'withdraw_id' => $withdrawId,
            'transaction_no' => $transactionNo,
            'contact_info' => $contactInfo,
            'detail_info' => $detailInfo,
            'photo_url' => $photoUrl,
        ];
        $this->withdrawAppealRepository->create($data);

    }

    /**
     * 用戶設定新的提現帳戶資料
     */
    public function addBankInfo($userId, $paymentChannelsSlug, $account, $bankSlug)
    {
        if ($paymentChannelsSlug == PaymentChannel::ALIPAY_PAY_CHANNEL_SLUG) {
            return $this->addBankInfoForAli($userId, $account);
        }

        if ($paymentChannelsSlug == PaymentChannel::BANK_CARD_CHANNEL_SLUG) {
            return $this->addBankInfoForBankCard($userId, $account, $bankSlug);
        }

        throw new \Exception(__('userBankInfo.invalid_payment_channel_slug'));
    }


    /**
     * 用戶新增支付寶資訊
     */
    public function addBankInfoForAli($userId, $account)
    {
        $ali = PaymentChannel::ALIPAY_PAY_CHANNEL_SLUG;

        $current =  $this->userBankInfoRepository->findWhere(
            [
                'user_id' => $userId,
                'payment_channel_slug' => $ali,    
            ]
        )->all();
        
        if (count($current) >= 5) {
            throw new \Exception(__('response.code.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }

        $data = [
            'user_id' => $userId,
            'payment_channel_slug' => $ali,
            'account' => $account,
        ];
        $this->userBankInfoRepository->create($data);
    }

    /**
     * 用戶新增銀行卡資訊
     */
    public function addBankInfoForBankCard($userId, $account, $bankSlug)
    {
        $bank = PaymentChannel::BANK_CARD_CHANNEL_SLUG;
        $current =  $this->userBankInfoRepository->findWhere(
            [
                'user_id' => $userId,
                'payment_channel_slug' => $bank,    
            ]
        )->all();
        
        if (count($current) >= 5) {
            throw new \Exception(__('response.code.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }

        $data = [
            'user_id' => $userId,
            'payment_channel_slug' => $bank,
            'account' => $account,
            'bank_slug' => $bankSlug,
        ];
        $this->userBankInfoRepository->create($data);
    }

    /**
     * 移除用戶的提現帳戶資料
     */
    public function removeBankInfo($userId, $bankInfoId)
    {
        $this->userBankInfoRepository->deleteWhere([
            'id' => $bankInfoId,
            'user_id' => $userId
        ]);
    }

    /**
     * 設為常用
     */
    public function setBankInfoToUsual($userId, $bankInfoId)
    {
        $model = $this->userBankInfoRepository->findWhere([
            'id' => $bankInfoId,
            'user_id' => $userId
        ])->first();
        if ($model == null) {
            throw new \Exception(__('response.code.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }
        if ($model->is_usual == 1) {
            return;
        }

        $model->is_usual = 1;
        $model->save();
        $paymentChannelsSlug = $model->payment_channel_slug;

        $allModel = $this->userBankInfoRepository->findWhere([
            'payment_channel_slug' => $paymentChannelsSlug,
            'user_id' => $userId
        ])->all();

        for ($i=0;$i<count($allModel);$i++) {
            if($allModel[$i]->id == $model->id) {
                continue;
            }
            if ($allModel[$i]->is_usual == 1) {
                $allModel[$i]->is_usual = 0;
                $allModel[$i]->save();
            }
        }
    }

    /**
     * 用戶更新帳戶資料
     */
    public function updateBankInfo($userId, $bankInfoId, $account, $name, $otherInfo)
    {
        $bankInfoModel = $this->userBankInfoRepository->findWhere(['id' => $bankInfoId])->first();
        if ($bankInfoModel == null) {
            throw new \Exception(__('userBankInfo.bank_info_id_not_found'));
        }

        if ($bankInfoModel->user_id != $userId) {
            throw new \Exception(__('userBankInfo.uesr_id_not_match'));
        }

        $bankInfoModel->account = $account;
        $bankInfoModel->name = $name;
        if ($otherInfo != null) {
            $bankInfoModel->other_info = $otherInfo;
        }

        $bankInfoModel->save();
    }

    /**
     * 取得提現帳戶資訊
     */
    public function getBankInfo($userId)
    {
        $realNameUserModel = $this->realNameVerifyRepository->findWhere(['user_id' => $userId])->first();
        if ($realNameUserModel == null) {
            throw new \Exception(__('user.user_not_pass_real_name_verify'));
        }

        $allUserBankModelArray = $this->userBankInfoRepository->findWhere(
            [
                'user_id' => $userId,
            ]
        )->all();
        if (count($allUserBankModelArray) == 0) {
            $data = [
                'user_id' => $userId,
                'payment_channel_slug' => PaymentChannel::ALIPAY_PAY_CHANNEL_SLUG,
                'account' => $realNameUserModel->alipay_account,
                'is_usual' => 1,
            ];
            $bankInfoModel = $this->userBankInfoRepository->create($data);
            $allUserBankModelArray[] = $bankInfoModel;
        }
        $result[PaymentChannel::ALIPAY_PAY_CHANNEL_SLUG] = [];
        $result[PaymentChannel::BANK_CARD_CHANNEL_SLUG] = [];

        for ($i = 0; $i < count($allUserBankModelArray); $i++) {
            $type = $allUserBankModelArray[$i]->payment_channel_slug;
            if ($type != PaymentChannel::ALIPAY_PAY_CHANNEL_SLUG && $type != PaymentChannel::BANK_CARD_CHANNEL_SLUG) {
                continue;
            }
            $result[$type][] = [
                'bank_info_id' => $allUserBankModelArray[$i]->id,
                'account' => $allUserBankModelArray[$i]->account,
                'is_usual' => $allUserBankModelArray[$i]->is_usual,
                'bank_slug' => $allUserBankModelArray[$i]->bank_slug,
            ];
        }

        return $result;
    }

    /**
     * 取得用戶提現金幣資訊
     */
    public function getGoldInfo($userId)
    {
        $userModel = $this->userRepository->findWhere([
            'id' => $userId,
        ])->first();
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }

        $times = $this->getCanWithdrawTimes($userId, true);

        $coinRatio = sc('coinRatio');
        $result = [
            'gold' => $userModel->real_withdraw_gold,
            'rmb' => (int) ($userModel->real_withdraw_gold / $coinRatio),
            'can_withdraw_times' => $times,
            'can_withdraw_rmb' => $times * UserDailyWithdrawTimes::MAX_WITHDRAW_RMB,
        ];
        return $result;
    }

    /**
     * 取得申訴圖片上傳的token和路徑
     */
    public function getUploadPhotoTokenAndFilePath($userId, $withdrawId)
    {
        // 用user_id找出相對應的用戶
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }

        // 依照用戶user id & withdrawId, 組成上傳檔案的路徑
        $fullPathName = $this->getPhotoUploadFilePath($userId, $withdrawId);

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
    protected function getPhotoUploadFilePath($userId, $withdrawId)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_PHOTO_FILENAME_PREFIX . $userId . $withdrawId . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_PHOTO_ROOT_DIR . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 取可以提現次數
     */
    public function getCanWithdrawTimes($userId, $forceUpdate = false)
    {
        $userWithdrawTimesModel = $this->userDailyWithdrawTimesRepository->findWhere([
            'user_id' => $userId,
        ])->first();
        if ($userWithdrawTimesModel == null) {
            $userWithdrawTimesModel = $this->userDailyWithdrawTimesRepository->create([
                'user_id' => $userId,
                'times' => UserDailyWithdrawTimes::DAILY_WITHDRAW_TIMES,
            ]);
        }
        $times = $userWithdrawTimesModel->times;

        $lastUpdateDate = Carbon::parse($userWithdrawTimesModel->updated_at);
        $now = Carbon::now();
        $isSameDay = $now->isSameDay($lastUpdateDate);

        if ($isSameDay == false) {
            if ($forceUpdate) {
                $times = UserDailyWithdrawTimes::DAILY_WITHDRAW_TIMES;
                $userWithdrawTimesModel->times = UserDailyWithdrawTimes::DAILY_WITHDRAW_TIMES;
                $userWithdrawTimesModel->save();
            }
            return UserDailyWithdrawTimes::DAILY_WITHDRAW_TIMES;
        }
        return $times;
    }

    /**
     * 提現申訴審核紀錄寫入
     */
    public function appealReviewSave($appealId, $title, $content, $review, $opAdminId)
    {

        $appealModel = $this->withdrawAppealRepository->findWhere(['id' => $appealId])->first();

        if ($review == WithdrawAppealModel::STATUS_SUCCESS) {
            $appealModel->status = WithdrawAppealModel::STATUS_SUCCESS;
        } else {
            $appealModel->status = WithdrawAppealModel::STATUS_FAIL;
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
     * 新增
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-10T16:03:27+0800
     */
    public function sendApply($data)
    {
        $userId = $data['user_id'];
        $userModel = $this->userRepository->makeModel()->where(['id' => $userId])->lockForUpdate()->first();
        // dd($userModel->toArray());
        if (!$userModel->isCanDraw()) {
            throw new \Exception(__('withDrawGoldApply.cannot_withdraw'));
        }
        $times = $this->getCanWithdrawTimes($userId, true);
        if ($times <= 0) {
            throw new \Exception(__('withDrawGoldApply.daily_withdraw_times_limit'));
        }
        if ($userModel->real_withdraw_gold < $data['gold']) {
            throw new \Exception(__('withDrawGoldApply.real_withdraw_gold_not_enough'));
        }

        try {
            $data['status'] = $this->repository->makeModel()::STATUS_NO;
            //取得当时手续费设定
            $paymentChannel = $this->paymentChannelRepository->findWhere(['slug' => $data['payment_channels_slug']])->first();
            if (!$paymentChannel) {
                throw new \Exception(__('withDrawGoldApply.illeagl_slug'));
            }
            $data['fee_type'] = $paymentChannel->fee_type;
            $data['fee'] = $paymentChannel->fee;
            $data['profit'] = 0;
            $data['transaction_no'] = $this->repository->makeModel()->generateTransactionNo();
            $newRecord = $this->repository->create($data);

            // 減少當日可提領次數
            $userWithdrawTimesModel = $this->userDailyWithdrawTimesRepository->findWhere([
                'user_id' => $userId,
            ])->first();
            $userWithdrawTimesModel->times -= 1;
            $userWithdrawTimesModel->save();

            // 減少可提現額度
            $userModel->real_withdraw_gold -= $data['gold'];
            $userModel->save();

            $userCurrentGold = $userModel->gold;
            if ($userCurrentGold <= $data['gold']) {
                throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
            }
            $userModel->addGold(-1 * $data['gold'], $userWithdrawTimesModel);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
        return $newRecord;
    }

    /**
     * 修改存档
     *
     * @param    [type]                   $id   [description]
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-12T09:01:36+0800
     */
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
     * 全部资料
     *
     * @param    array                    $where   [description]
     * @param    array                    $columns [description]
     * @param    integer                  $offset  [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-12T11:13:46+0800
     */
    public function all($where = [], $columns = [], $offset = 1)
    {
        $offset = ($offset - 1) * config('app.api_per_page_data');
        $orderByColumn = 'created_at';
        $limit = config('app.api_per_page_data');

        $result = $this->repository->with(['payment_channel'])->scopeQuery(function ($query) use ($orderByColumn) {
            return $query->orderBy($orderByColumn, 'desc');
        })->findWhere($where)
            ->map(function ($item) {
                $item->alias = $item->payment_channel->title ?? '';
                return $item;
            })
            ->toArray();
        return $result;
    }

    /**
     * 审核提现申请
     *
     * @param    WithDrawGoldApply        $apply   [description]
     * @param    [type]                   $status  [description]
     * @param    string                   $comment [description]
     *
     * @return   WithDrawGoldApply         $apply                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-12T11:21:39+0800
     */
    public function review(WithDrawGoldApply $apply, $status, $comment = "", $fromSourceUserId = 0)
    {
        try {
            $userId = $apply->user_id;
            $userModel = $this->userRepository->findWhere([
                'id' => $userId,
            ])->first();

            switch ($status) {
                case WithDrawGoldApply::STATUS_PROCESSING:
                    //要计算手续费
                    $amount = goldToCoin($apply->gold);
                    $fee = app(PaymentChannelService::class)->feeCalcuate($apply->payment_channels_slug, $amount);
                    $profit = $amount - $fee;
                    $apply->profit = $profit;
                    $apply->cost = $amount - $apply->profit;
                    $apply->admin_id = adminId();
                    $apply->comment = $comment;
                    $apply->status = $status;
                    if ($fromSourceUserId != 0) {
                        $apply->from_source_user_id = $fromSourceUserId;
                    }
                    $apply->save();
                    break;
                case WithDrawGoldApply::STATUS_PASS:
                    $apply->comment = $comment;
                    $apply->status = $status;
                    $apply->save();
                    break;
                case WithDrawGoldApply::STATUS_REJECT:
                    $apply->comment = $comment;
                    $apply->status = $status;
                    $apply->save();
                    // 反還可提現額度
                    $userModel->real_withdraw_gold += $apply->gold;
                    $userModel->save();
                    $userModel->addGold($apply->gold, $apply);
                    break;
                case WithDrawGoldApply::STATUS_CANCEL:
                    $apply->status = $status;
                    $apply->save();
                    // 反還可提現額度
                    $userModel->real_withdraw_gold += $apply->gold;
                    $userModel->save();
                    $userModel->addGold($apply->gold, $apply);
                    break;
                default:
                    throw new \Exception(__('withDrawGoldApply.illegal_opeation'));
                    break;
            }
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
        return $apply;
    }

}
