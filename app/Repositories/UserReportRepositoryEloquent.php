<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserReportRepository;
use App\Models\UserReport;
use App\Validators\UserReportValidator;

/**
 * Class UserReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserReportRepositoryEloquent extends BaseRepository implements UserReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
