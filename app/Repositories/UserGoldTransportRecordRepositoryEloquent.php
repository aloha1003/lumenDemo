<?php

namespace App\Repositories;

use App\Models\UserGoldTransportRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserGoldTransportRecordRepository;

/**
 * Class UserGoldTransportRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserGoldTransportRecordRepositoryEloquent extends BaseRepository implements UserGoldTransportRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserGoldTransportRecord::class;
    }

}
