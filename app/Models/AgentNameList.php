<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AgentNameList.
 *
 * @package namespace App\Models;
 */
class AgentNameList extends Model implements Transformable
{
    use TransformableTrait;

    protected $isLog = false;

    public $table = "agent_name_list";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_user_id',
        'trace_user_id',
        'is_star'
    ];

}
