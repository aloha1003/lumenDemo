<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnchorAdversting.
 *
 * @package namespace App\Models;
 */
class AnchorAdversting extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;
    const ADV_TYPE_HOT = 'hot';
    const ADV_TYPE_RECOMMEND = 'recommand';
    const ADV_TYPE_NEW = 'new';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function getAdvTypeTitle()
    {
        return __('anchor_adversting.anchor_adv_type');
    }

}
