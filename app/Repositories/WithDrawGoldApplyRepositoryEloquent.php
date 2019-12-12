<?php

namespace App\Repositories;

use App\Models\WithDrawGoldApply;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\withDrawGoldApplyRepository;

/**
 * Class WithDrawGoldApplicationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WithDrawGoldApplyRepositoryEloquent extends BaseRepository implements WithDrawGoldApplyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WithDrawGoldApply::class;
    }

}
