<?php

namespace App\Repositories;

use App\Models\BetterUserView;
use App\Models\User;
use App\Models\UserAdminView;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Services\UserService;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    const AUTH_PHONE = 'phone';
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function isSkippedCache()
    {
        return true;
    }

    public function create(array $parameters)
    {
        $parentParameters = $parameters;
        unset($parentParameters['password'], $parentParameters['auth_type']);
        $model = parent::create($parentParameters);
        $authParameters = collect($parameters)->only(['auth_type', 'password', 'cellphone', 'wechat', 'qq', 'weibo'])->toArray();
        $authParameters['auth_type'] = $authParameters['auth_type'] ?? self::AUTH_PHONE;
        switch ($authParameters['auth_type']) {
            case self::AUTH_PHONE:
            default:
                unset($authParameters['auth_type']);
                $model->auth()->create($authParameters);
                break;
        }
        return $model;
    }

    public function findByID($id)
    {
        $model = new \App\Models\User();
        $query = $model->newQuery();
        $result = $query->where('id', '=', $id)->get(['id']);
        return $result;
    }

    public function findByCellphone($cellphone)
    {
        $model = new \App\Models\User();
        $query = $model->newQuery();
        $result = $query->where('cellphone', '=', $cellphone)->get(['id']);
        return $result;
    }

    public function updateGold($userModel, $gold, $sourceModel)
    {
        $userModel->setRealGold($userModel->id, $gold, $sourceModel);
    }

    /**
     * 增加金幣
     */
    public function addGold($userModel, $gold, $sourceModel, $updateCache = false, $updateCacheCallback = null)
    {
        $userModel->addGold($gold, $sourceModel, $updateCache, $updateCacheCallback);
    }

    /**
     * 取得优质用户View
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-12T09:31:20+0800
     */
    public function getBetterUserView()
    {
        return app(BetterUserView::class);
    }

    /**
     * 取得后台用户管理View
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-12T09:31:39+0800
     */
    public function getUserAdminView()
    {
        return app(UserAdminView::class);
    }

    public function selectNickNameWithFullText($searchText)
    {
        $userDataArray = \DB::select("SELECT * FROM user WHERE MATCH (nickname) AGAINST (? IN NATURAL LANGUAGE MODE);", [$searchText]);

        $modelArray = [];

        for ($i = 0; $i < count($userDataArray); $i++) {
            $user = new User((array) $userDataArray[$i]);
            $modelArray[] = $user;
        }
        return collect($modelArray);
    }

}
