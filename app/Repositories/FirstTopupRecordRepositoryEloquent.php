<?php

namespace App\Repositories;

use App\Models\FirstTopupRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\firstTopupRecordRepository;

/**
 * Class FirstTopupRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FirstTopupRecordRepositoryEloquent extends BaseRepository implements FirstTopupRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FirstTopupRecord::class;
    }

}
