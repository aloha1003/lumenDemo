<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmLogImeRegister.
 *
 * @package namespace App\Models;
 */
class GmLogImeRegister extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'log_ime_register';
    public $timestamps = false;
    protected $connection = 'mysql_game';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ime', 'gid', 'reg_time', 'reg_num', 'reg_channel'];

}
