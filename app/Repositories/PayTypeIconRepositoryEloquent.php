<?php

namespace App\Repositories;

use App\Models\PayTypeIcon;
use App\Repositories\Interfaces\PayTypeIconRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class PayTypeIconRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PayTypeIconRepositoryEloquent extends BaseRepository implements PayTypeIconRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PayTypeIcon::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
