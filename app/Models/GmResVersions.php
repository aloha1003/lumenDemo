<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmResVersions.
 *
 * @package namespace App\Models;
 */
class GmResVersions extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'gm_res_versions';
    public $timestamps = false;
    protected $connection = 'mysql_game';
    protected $isLog = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'gameid', 'asseturl', 'res_ver', 'gitcm', 'major', 'isforce', 'ctime', 'is_release'];

}
