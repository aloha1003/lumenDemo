<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GiftPropotion.
 *
 * @package namespace App\Models;
 */
class GiftPropotion extends Model implements Transformable
{
    use TransformableTrait;
    protected $isLog = false;
    public $table = 'gift_propotion';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['gift_type_id', 'receives_times', 'anchor_propotion', 'company_propotion'];

}
