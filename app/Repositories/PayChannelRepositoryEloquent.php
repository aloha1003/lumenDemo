<?php

namespace App\Repositories;

use App\Models\PayChannel;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\payChannelRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PayChannelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PayChannelRepositoryEloquent extends BaseRepository implements PayChannelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PayChannel::class;
    }

      

}
