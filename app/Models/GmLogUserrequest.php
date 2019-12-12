<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmLogUserrequest.
 *
 * @package namespace App\Models;
 */
class GmLogUserrequest extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'log_userrequst';
    public $timestamps = false;
    protected $connection = 'mysql_game';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gid', 
        'keyword', 
        'resversion', 
        'osver', 
        'appver', 
        'lineNo', 
        'uuid', 
        'simSerial', 
        'dev_id', 
        'channel', 
        'ctime', 
        'request_ip', 
        'city', 
        'city_id', 
        'isp'
    ];

}
