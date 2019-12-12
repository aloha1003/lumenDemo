<?php

namespace App\Repositories;

use App\Models\PaymentChannel;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\paymentChannelRepository;

/**
 * Class PaymentChannelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentChannelRepositoryEloquent extends BaseRepository implements PaymentChannelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PaymentChannel::class;
    }

}
