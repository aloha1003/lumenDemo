<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\WithdrawAppealRepository;
use App\Models\WithdrawAppeal;
use App\Validators\WithdrawAppealValidator;

/**
 * Class WithdrawAppealRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WithdrawAppealRepositoryEloquent extends BaseRepository implements WithdrawAppealRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WithdrawAppeal::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
