<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnnouceForUserRepository;
use App\Models\AnnouceForUser;
use App\Validators\AnnouceForUserValidator;

/**
 * Class AnnouceForUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnnouceForUserRepositoryEloquent extends BaseRepository implements AnnouceForUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnnouceForUser::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
