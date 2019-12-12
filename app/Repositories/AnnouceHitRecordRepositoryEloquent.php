<?php

namespace App\Repositories;

use App\Models\AnnouceHitRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnnouceHitRecordRepository;

/**
 * Class AnnouceHitRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnnouceHitRecordRepositoryEloquent extends BaseRepository implements AnnouceHitRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnnouceHitRecord::class;
    }

}
