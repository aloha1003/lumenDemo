<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserBankInfoRepository;
use App\Models\UserBankInfo;
use App\Validators\UserBankInfoValidator;

/**
 * Class UserBankInfoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserBankInfoRepositoryEloquent extends BaseRepository implements UserBankInfoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserBankInfo::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
