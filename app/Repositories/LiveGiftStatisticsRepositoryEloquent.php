<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LiveGiftStatisticsRepository;
use App\Models\LiveGiftStatistics;
use App\Validators\LiveGiftStatisticsValidator;

/**
 * Class LiveGiftStatisticsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LiveGiftStatisticsRepositoryEloquent extends BaseRepository implements LiveGiftStatisticsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LiveGiftStatistics::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
