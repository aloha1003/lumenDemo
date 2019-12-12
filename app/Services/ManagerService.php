<?php

namespace App\Services;

use App\Exceptions\ErrorCode;
use App\Models\CompanyWithdraw;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\CompanyWithdrawRepository;
use App\Repositories\Interfaces\ManagerCompanyMoneyFlowRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\PaymentChannelRepository;
use App\Repositories\Interfaces\UserRepository;

//经纪人服务
class ManagerService
{
    protected $repository;
    protected $userRepository;
    protected $anchorInfoRepository;
    protected $companyWithdrawRepository;
    protected $paymentChannelRepository;
    protected $managerCompanyMoneyFlowRepository;

    public function __construct(
        ManagerRepository $repository,
        UserRepository $userRepository,
        AnchorInfoRepository $anchorInfoRepository,
        CompanyWithdrawRepository $companyWithdrawRepository,
        PaymentChannelRepository $paymentChannelRepository,
        ManagerCompanyMoneyFlowRepository $managerCompanyMoneyFlowRepository) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->anchorInfoRepository = $anchorInfoRepository;
        $this->companyWithdrawRepository = $companyWithdrawRepository;
        $this->paymentChannelRepository = $paymentChannelRepository;
        $this->managerCompanyMoneyFlowRepository = $managerCompanyMoneyFlowRepository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function obtain($id)
    {
        return $this->repository->skipCriteria()->find($id);
    }

    public function all($query = [], $columns = ['*'])
    {
        if ($query) {
            $this->repository->injectSearch($query)->skipCriteria(false);
        }
        return $this->repository->paginate(null, $columns);
    }

    public function allSkipCriteria($columns = ['*'])
    {
        return $this->repository->skipCriteria(true)->findWhere(['role' => $this->repository->makeModel()::ROLE_MANAGER], $columns);
    }

    public function findByCompany($companyId, $columns = ['*'])
    {
        return $this->repository->findWhere([
            'role' => $this->repository->makeModel()::ROLE_MANAGER,
            'parent_id' => $companyId,
        ], $columns);
    }

    public function store($data)
    {
        if (isset($data['id'])) {
            $this->repository->update($data, $data['id']);
        } else {
            $this->repository->create($data);
        }
        return true;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function getCompanyId($adminId)
    {
        $managerModel = $this->repository->findWhere(['id' => $adminId])->first();

        if ($managerModel->parent_id != 0) {
            return $managerModel->parent_id;
        }
        return $adminId;
    }

    /**
     * 用company id取得所有主播id
     *
     * @param int $companyId
     *
     * @return array
     */
    public function getAllAnchorIdByCompanyId($companyId)
    {
        $allAnchorModel = $this->anchorInfoRepository->findWhere(['company_id' => $companyId])->all();
        $result = [];

        for ($i = 0; $i < count($allAnchorModel); $i++) {
            $result[] = $allAnchorModel[$i]->user_id;
        }
        return $result;
    }

    /**
     * 建立一筆經紀公司申請提現資料
     */
    public function createCompanyWithdraw($companyId, $paymentAccount, $withdrawRmb, $paymentChannelSlug, $companyComment, $ratio = 1)
    {
        $withdrawMoneyData = $this->getMoneyByRmbAndRatio($withdrawRmb, $ratio);
        $withdrawRmb = $withdrawMoneyData['rmb'];
        $withdrawGold = $withdrawMoneyData['gold'];

        $paymentChannel = $this->paymentChannelRepository->findWhere(['slug' => $paymentChannelSlug])->first();

        $data = [
            'company_id' => $companyId,
            'target_anchor_id' => CompanyWithdraw::COMPANY_WITHDRAW_SELF,
            'payment_account' => $paymentAccount,
            'withdraw_gold' => $withdrawGold,
            'withdraw_rmb' => $withdrawRmb,
            'payment_channels_slug' => $paymentChannelSlug,
            'compnay_comment' => $companyComment,
            'fee_type' => $paymentChannel->fee_type,
            'fee' => $paymentChannel->fee,
        ];

        $managerCollection = $this->repository->findWhere(['id' => $companyId]);
        $managerModel = $managerCollection->first();
        $newGold = $managerModel->gold - $withdrawGold;
        $withdrawModel = $this->companyWithdrawRepository->create($data);

        $this->repository->updateGold($managerModel, $newGold, $withdrawModel);
    }

    /**
     * 建立一筆經紀公司幫主播申請提現的資料
     */
    public function createAnchorWithdrawByCompany($companyId, $anchorId, $paymentAccount, $withdrawRmb, $paymentChannelSlug, $companyComment, $ratio = 1)
    {
        $withdrawMoneyData = $this->getMoneyByRmbAndRatio($withdrawRmb, $ratio);
        $withdrawRmb = $withdrawMoneyData['rmb'];
        $withdrawGold = $withdrawMoneyData['gold'];

        $paymentChannel = $this->paymentChannelRepository->findWhere(['slug' => $paymentChannelSlug])->first();

        $data = [
            'company_id' => $companyId,
            'target_anchor_id' => $anchorId,
            'payment_account' => $paymentAccount,
            'withdraw_gold' => $withdrawGold,
            'withdraw_rmb' => $withdrawRmb,
            'payment_channels_slug' => $paymentChannelSlug,
            'compnay_comment' => $companyComment,
            'fee_type' => $paymentChannel->fee_type,
            'fee' => $paymentChannel->fee,
        ];
        $userCollection = $this->userRepository->findWhere(['id' => $anchorId]);
        $userModel = $userCollection->first();
        if ($userModel->gold_cache < $withdrawGold) {
            throw new \Exception(__('user.gold_not_enough'), ErrorCode::USER_GOLD_NOT_ENOUGH);
        }
        $newGold = $userModel->gold - $withdrawGold;
        $withdrawModel = $this->companyWithdrawRepository->create($data);

        $this->userRepository->addGold($userModel, -1 * $withdrawGold, $withdrawModel);
    }

    /**
     * 依照幣值與rmb來計算金幣與rmb
     */
    public function getMoneyByRmbAndRatio($rmb, $ratio = 1)
    {
        $realGold = $rmb * $ratio;

        return ['gold' => $realGold, 'rmb' => $rmb];
    }

    /**
     * 審核提現資料
     */
    public function reviewWithdrawWithModel($model, $status, $realRmb, $comment, $adminId)
    {
        $model->status = $status;
        // 審核通過, 要寫入經紀公司現金流水
        if ($model->status == CompanyWithdraw::STATUS_FINISH) {
            if ($realRmb == '') {
                $model->real_withdraw_rmb = $model->withdraw_rmb;
            }
            $model->real_withdraw_rmb = $realRmb;

            $cost = app(PaymentChannelService::class)->feeCalcuate($model->payment_channels_slug, $model->withdraw_rmb);
            $model->cost = $cost;

            $flowData = [
                'company_id' => $model->company_id,
                'rmb' => $model->real_withdraw_rmb,
                'source_model_name' => get_class($model),
                'source_model_primary_key_column' => $model->getKeyName(),
                'source_model_primary_key_id' => $model->getKey(),
            ];

            $this->managerCompanyMoneyFlowRepository->create($flowData);
        } else {
            $model->real_withdraw_rmb = 0;
            $model->cost = 0;
        }

        $model->csr_comment = $comment;
        $model->admin_id = $adminId;

        $withdrawGold = $model->withdraw_gold;
        // 審核被駁回, 要返還金幣
        if ($status == CompanyWithdraw::STATUS_REJECT) {
            if ($model->target_anchor_id == CompanyWithdraw::COMPANY_WITHDRAW_SELF) {
                //返還經紀公司金幣
                $companyModel = $this->repository->findWhere(['id' => $model->company_id])->first();
                $newGold = $companyModel->gold + $withdrawGold;
                $this->repository->updateGold($companyModel, $newGold, $model);
            } else {
                //返還主播金幣
                $userModel = $this->userRepository->findWhere(['id' => $model->target_anchor_id])->first();
                $newGold = $userModel->gold + $withdrawGold;
                $this->userRepository->addGold($userModel, $withdrawGold, $model);
            }
        }

        $model->save();
    }
}
