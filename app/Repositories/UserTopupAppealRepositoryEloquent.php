<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserTopupAppealRepository;
use App\Models\UserTopupAppeal;
use App\Validators\UserTopupAppealValidator;

/**
 * Class UserTopupAppealRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTopupAppealRepositoryEloquent extends BaseRepository implements UserTopupAppealRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTopupAppeal::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
