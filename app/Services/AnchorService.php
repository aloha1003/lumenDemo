<?php

namespace App\Services;

use App\Models\CompanyAnchorApply;
use App\Models\User as UserModel;
use App\Models\UserAuth;
use App\Repositories\Interfaces\AnchorAdverstingRepository;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\CompanyAnchorApplyRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\RealNameVerifyRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Services\UserService;

//主播服务
class AnchorService
{
    protected $repository;
    private $anchorAdverstingRepository;
    private $realNameVerifyRepository;
    private $userRepository;

    private $managerRepository;

    const ANCHOR_APPLY_PHOTO_PATH = 'manager/anchor/apply/image';

    public function __construct(
        AnchorInfoRepository $repository,
        UserRepository $userRepository,
        RealNameVerifyRepository $realNameVerifyRepository,
        AnchorAdverstingRepository $anchorAdverstingRepository,
        CompanyAnchorApplyRepository $companyAnchorApplyRepository,
        ManagerRepository $managerRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->managerRepository = $managerRepository;

        $this->anchorAdverstingRepository = $anchorAdverstingRepository;
        $this->realNameVerifyRepository = $realNameVerifyRepository;
        $this->companyAnchorApplyRepository = $companyAnchorApplyRepository;

    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function all()
    {
        return $this->repository->with(['user', 'manager', 'live' => function ($query) {
            $query->select([\DB::raw('sum(duration) as total_durations'), 'user_id']);
            $query->groupBy('user_id');
        }])->paginate();
    }

    public function store($data)
    {
        $this->repository->create($data);
    }

    public function obtain($id)
    {
        return $this->repository->with(['user', 'realNameVerify', 'anchorAdverstings'])->find($id);
    }
    public function anchorStore($id, $data)
    {
        $anchor = $this->obtain($id);
        $anchor->can_live = $data['can_live'] ?? $anchor->can_live;
        $anchor->save();
        $adModel = $this->anchorAdverstingRepository->makeModel();
        if (isset($data['anchor_type'])) {
            $data['anchor_type'] = array_filter($data['anchor_type']);
            $anchor->syncAnchorAdversting($data['anchor_type']);
        }
        if ($anchor->realNameVerify) {
            if ($anchor->realNameVerify->is_confirm == \App\Models\RealNameVerify::IS_CONFIRM_REJECT) {
                //写入实名认证
                //处理图片
                $photoPath = $this->uploadRealNamePhoto($data['photo']);
                $syncRealNameVerifyData = [
                    'real_name' => $data['real_name'],
                    'no' => $data['no'],
                    'cellphone' => $data['cellphone'],
                    'alipay_account' => $data['alipay_account'],
                    'photo' => $photoPath,
                ];
                $anchor->realNameVerify()->save($syncRealNameVerifyData);
            }
        } else {
            //写入实名认证
            //处理图片
            $photoPath = $this->uploadRealNamePhoto($data['photo']);
            $syncRealNameVerifyData = [
                'real_name' => $data['real_name'],
                'no' => $data['no'],
                'cellphone' => $data['cellphone'],
                'alipay_account' => $data['alipay_account'],
                'photo' => $photoPath,
            ];
            $anchor->realNameVerify()->create($syncRealNameVerifyData);
        }
    }

    private function uploadRealNamePhoto($photo)
    {
        $ext = $photo->getClientOriginalExtension();
        $photoPath = $this->realNameVerifyRepository->makeModel()::PHOTO_STORE_ROOT . '/' . date("Y-m-d");
        $photoPathName = date("Y-m-d") . '-' . uniqid() . '.' . $ext;
        $res = \CLStorage::upload($photoPath, $photo, $photoPathName);
        return $res;
    }
    /**
     * 從cache取得所有AnchorInfo資料
     */
    public function getAnchorInfoDataFromCache($userId)
    {
        $userService = app(UserService::class);
        $key = $userService->getAnchorInfoCacheKey($userId);
        return \Cache::get($key);
    }

    /**
     * 設置AnchorInfo資料到cache中
     */
    public function setAnchorInfoDataToCache($userId, $data)
    {
        $userService = app(UserService::class);
        $key = $userService->getAnchorInfoCacheKey($userId);
        \Cache::forever($key, $data);
    }

    /**
     * 審核主播申請資料
     */
    public function anchorApplyReview($model, $adminId, $status, $comment)
    {
        // 審核通過新增一名主播

        if ($status == CompanyAnchorApply::SUCCESS_STATUS) {

            $realNameModel = $this->realNameVerifyRepository->findWhere([
                'no' => $model->no,
            ])->first();
            if ($realNameModel != null) {
                throw new \Exception(__('anchorApply.id_card_exist'));
            }
            $realNameModel = $this->realNameVerifyRepository->findWhere([
                'cellphone' => $model->cellphone,
            ])->first();
            if ($realNameModel != null) {
                throw new \Exception(__('anchorApply.cellphonw_exist'));
            }

            $userService = app(UserService::class);
            $nowYear = date("Y", time());
            $userData = [
                'level' => 0,
                'password' => bcrypt(UserAuth::passwordEncry($model->password)),
                'user_type_id' => UserModel::USER_TYPE_ANCHOR,
                'cellphone' => $model->cellphone,
                'nickname' => $userService::DEFAULT_USER_NICKNAME_PREFIX . $model->cellphone,
                'sex' => UserModel::SEX_UNKNOW,
                'birthday' => ($nowYear - 18) . date("-m-d", time()),
                'avatar' => config('app.avatar'),
                'channel' => config('app.channel'),
                'os_version' => config('app.os_version'),
                'gold' => 0,
                'manager_id' => $model->manager_id,
                'auth_type' => $this->userRepository::AUTH_PHONE,

            ];
            $user = $this->userRepository->create($userData);

            $syncrealNameVerifyData = [
                'real_name' => $model->real_name,
                'no' => $model->no,
                'cellphone' => $model->cellphone,
                'alipay_account' => $model->alipay_account,
                'photo' => $model->photo,
                'is_confirm' => $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_PASS,
            ];
            $user->real_name_verify()->create($syncrealNameVerifyData);
            $manager = $this->managerRepository->find($model->manager_id);

            $syncAnchorData = [
                'user_id' => $user->id,
                'company_id' => $manager->company_id,
                'manager_id' => $model->manager_id,
                'front_cover' => config('app.avatar'),
            ];
            $user->anchor()->create($syncAnchorData);

            // 將user資料的暱稱改為 prefix + id
            $user->nickname = $userService->defaultNickName($user);

            // 儲存修改暱稱
            $user->save();
        }
        $model->op_admin_id = $adminId;
        $model->status = $status;
        $model->op_admin_comment = $comment;
        $model->save();
    }

    /**
     * 寫入一筆主播申請資料
     */
    public function setAnchorApply($realName, $no, $photoFile, $alipayAccount, $cellphone, $password, $managerId, $companyId)
    {
        //$password = bcrypt(UserAuth::passwordEncry($password));
        $photoUrl = $this->uploadAnchorApplyPhoto($photoFile);
        $data = [
            'status' => CompanyAnchorApply::WAIT_STATUS,
            'real_name' => $realName,
            'no' => $no,
            'alipay_account' => $alipayAccount,
            'cellphone' => $cellphone,
            'password' => $password,
            'manager_id' => $managerId,
            'company_id' => $companyId,
            'photo' => $photoUrl,
        ];
        $this->companyAnchorApplyRepository->create($data);
    }

    /**
     * 再次審核主播申請
     */
    public function anchorApplyAgain($applyId, $realName, $no, $photoFile, $alipayAccount, $cellphone, $password, $managerId)
    {
        $model = $this->companyAnchorApplyRepository->findWhere(['id' => $applyId])->first();
        if ($model == null) {
            throw new \Exception(__('anchorApply.apply_not_found'));
        }

        //$password = bcrypt(UserAuth::passwordEncry($password));
        if ($photoFile != '') {
            $photoUrl = $this->uploadAnchorApplyPhoto($photoFile);
            $model->photo = $photoUrl;
        }

        $model->real_name = $realName;
        $model->no = $no;
        $model->alipay_account = $alipayAccount;
        $model->cellphone = $cellphone;
        $model->password = $password;
        $model->manager_id = $managerId;
        $model->status = CompanyAnchorApply::WAIT_STATUS;
        $model->save();
    }

    /**
     * 上傳主播申請圖片到cdn
     */
    public function uploadAnchorApplyPhoto($photoFile)
    {
        $imagePath = self::ANCHOR_APPLY_PHOTO_PATH;
        $url = \CLStorage::upload($imagePath, $photoFile);
        return $url;
    }
}
