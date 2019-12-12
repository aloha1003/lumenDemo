<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\BaseLevelRepository;
use App\Models\BaseLevel;
use App\Validators\BaseLevelValidator;

/**
 * Class BaseLevelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseLevelRepositoryEloquent extends BaseRepository implements BaseLevelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseLevel::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
