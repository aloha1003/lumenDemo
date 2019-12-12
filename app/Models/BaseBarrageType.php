<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BaseBarrageType.
 *
 * @package namespace App\Models;
 */
class BaseBarrageType extends Model implements Transformable
{
    use TransformableTrait;

    const BARRAGE_ID = 1;
    const TRANSPORT_ID = 2;

    private static $allModelData;

    public $table = 'base_barrage_type';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['gold_price', 'comment', 'onshow'];

    public function getStaticAllData()
    {
        if (self::$allModelData == null) {
            self::$allModelData = $this->all();
        }
        return self::$allModelData;
    }

}
