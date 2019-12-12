<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserTopupReport.
 *
 * @package namespace App\Models;
 */
class UserTopupReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['settle_date', 'pay_channels_slug', 'pay_channel_payments_pay_type', 'cost', 'amount', 'profit', 'gold', 'no_repeat_users', 'no_repeat_first_users'];

}
