<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LiveBarrageStatisticsRepository;
use App\Models\LiveBarrageStatistics;
use App\Validators\LiveBarrageStatisticsValidator;

/**
 * Class LiveBarrageStatisticsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LiveBarrageStatisticsRepositoryEloquent extends BaseRepository implements LiveBarrageStatisticsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LiveBarrageStatistics::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
