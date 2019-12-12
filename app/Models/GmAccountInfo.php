<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmAccountInfo.
 *
 * @package namespace App\Models;
 */
class GmAccountInfo extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'gm_account_info';
    protected $primaryKey = 'gid';
    public $timestamps = false;

    protected $connection = 'mysql_game';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gid', 
        'account_name', 
        'account_pwd', 
        'phone',
        'device_id', 
        'op_uuid', 
        'reg_type', 
        'reg_channel', 
        'reg_game', 
        'reg_time', 
        'last_login', 
        'token', 
        'areaId', 
        'token_invalid', 
        'ip_reg', 
        'ip_last', 
        'status', 
        'headurl', 
        'block_start', 
        'block_end',
        'remark', 
        'modify_at'
    ];
}
