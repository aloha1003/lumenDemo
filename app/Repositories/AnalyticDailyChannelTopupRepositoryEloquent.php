<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyChannelTopupRepository;
use App\Models\AnalyticDailyChannelTopup;
use App\Validators\AnalyticDailyChannelTopupValidator;

/**
 * Class AnalyticDailyChannelTopupRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyChannelTopupRepositoryEloquent extends BaseRepository implements AnalyticDailyChannelTopupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyChannelTopup::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
