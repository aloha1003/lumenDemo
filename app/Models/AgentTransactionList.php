<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AgentTransactionList.
 *
 * @package namespace App\Models;
 */
class AgentTransactionList extends Model implements Transformable
{
    use TransformableTrait;

    protected $isLog = false;

    public $table = "agent_transaction_list";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_user_id',
        'transaction_target_user_id',
        'transaction_gold',
        'agent_origin_gold',
        'agent_remain_gold',
        'user_origin_gold',
        'user_remain_gold',
        'comment'
    ];

}
