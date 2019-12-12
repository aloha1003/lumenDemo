<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserRetentionRateRepository;
use App\Models\UserRetentionRate;
use App\Validators\UserRetentionRateValidator;

/**
 * Class UserRetentionRateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRetentionRateRepositoryEloquent extends BaseRepository implements UserRetentionRateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserRetentionRate::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
