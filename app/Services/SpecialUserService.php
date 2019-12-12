<?php
namespace App\Services;

use App\Models\User as UserModel;
use App\Models\Channel as ChannelModel;
use App\Repositories\Interfaces\RealNameVerifyRepository;
use App\Repositories\Interfaces\SpecialUserRepository;
use App\Repositories\Interfaces\UserRepository;

//内定帐号设定服务
class SpecialUserService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    private $userRepository;
    private $realNameVerifyRepository;
    public function __construct(SpecialUserRepository $repository, UserRepository $userRepository, RealNameVerifyRepository $realNameVerifyRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->realNameVerifyRepository = $realNameVerifyRepository;
    }

    /**
     * 存档
     *
     * @param    id                   $id   主键
     * @param    array                   $data 输入资料
     *
     * @return   void                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:24:25+0800
     */
    public function save($id, $data)
    {
        try {
            $rollAd = $this->repository->find($id);
            $return = $rollAd->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   SpecialUser               新增成功的SpecialUser
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        try {
            //要先新增 User
            $userData = [
                'id' => $data['user_id'],
                'level' => 0,
                'user_type_id' => UserModel::USER_TYPE_NORMAL,
                'cellphone' => time(),
                'nickname' => uniqid(),
                'sex' => UserModel::SEX_UNKNOW,
                'avatar' => config('app.avatar'),
                'register_channel' => ChannelModel::TEST_CHANNEL,
            ];
            $user = $this->userRepository->makeModel();

            foreach ($userData as $key => $value) {
                $user->$key = $value;
            }
            $user->save();

            $spacialUser = $this->repository->create($data);
            return $spacialUser;
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }
    /**
     * 删除资料
     *
     * @param    [type]                   $id [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T13:15:14+0800
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
