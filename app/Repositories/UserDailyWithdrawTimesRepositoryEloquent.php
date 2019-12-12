<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserDailyWithdrawTimesRepository;
use App\Models\UserDailyWithdrawTimes;
use App\Validators\UserDailyWithdrawTimesValidator;

/**
 * Class UserDailyWithdrawTimesRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserDailyWithdrawTimesRepositoryEloquent extends BaseRepository implements UserDailyWithdrawTimesRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserDailyWithdrawTimes::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
