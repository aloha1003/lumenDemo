<?php

namespace App\Repositories;

use App\Models\RollAdHitRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\RollAdHitRecordRepository;

/**
 * Class RollAdHitRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RollAdHitRecordRepositoryEloquent extends BaseRepository implements RollAdHitRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RollAdHitRecord::class;
    }

}
