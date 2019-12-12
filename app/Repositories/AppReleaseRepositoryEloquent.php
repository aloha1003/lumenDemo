<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AppReleaseRepository;
use App\Models\AppRelease;
use App\Validators\AppReleaseValidator;

/**
 * Class AppReleaseRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AppReleaseRepositoryEloquent extends BaseRepository implements AppReleaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AppRelease::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
