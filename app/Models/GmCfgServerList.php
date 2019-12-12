<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmCfgServerList.
 *
 * @package namespace App\Models;
 */
class GmCfgServerList extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'cfg_serverlist';
    public $timestamps = false;
    protected $connection = 'mysql_game';
    protected $isLog = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'serverid', 'servername', 'serverip', 'serverport', 'status', 'server_type', 'connect', 'utime'];

}
