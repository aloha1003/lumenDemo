<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserGoldTransportRecord.
 *
 * @package namespace App\Models;
 */
class UserGoldTransportRecord extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;

    public $table = 'user_gold_transport_record';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gold_in_user_id',
        'gold_out_user_id',
        'transaction_gold',
        'gold_in_user_origin_gold',
        'gold_in_user_remain_gold',
        'gold_out_user_origin_gold',
        'gold_out_user_remain_gold',
        'op_admin_id',
        'comment',
    ];

}
