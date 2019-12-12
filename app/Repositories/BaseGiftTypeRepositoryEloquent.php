<?php

namespace App\Repositories;

use App\Models\BaseGiftType;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class BaseGiftTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseGiftTypeRepositoryEloquent extends BaseRepository implements BaseGiftTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseGiftType::class;
    }

      

}
