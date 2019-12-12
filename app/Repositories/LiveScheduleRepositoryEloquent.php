<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LiveScheduleRepository;
use App\Models\LiveSchedule;
use App\Validators\LiveScheduleValidator;

/**
 * Class LiveScheduleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LiveScheduleRepositoryEloquent extends BaseRepository implements LiveScheduleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LiveSchedule::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
