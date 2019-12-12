<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserLevelAccumulationRepository;
use App\Models\UserLevelAccumulation;
use App\Validators\UserLevelAccumulationValidator;

/**
 * Class UserLevelAccumulationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserLevelAccumulationRepositoryEloquent extends BaseRepository implements UserLevelAccumulationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserLevelAccumulation::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
