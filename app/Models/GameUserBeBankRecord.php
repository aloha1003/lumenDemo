<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GameUserBeBankRecord.
 *
 * @package namespace App\Models;
 */
class GameUserBeBankRecord extends Model implements Transformable
{
    use TransformableTrait;

    const ON_STATUS = 0;
    const OFF_STATUS = 1;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'channel', 'game_slug', 'status', 'bank_on_gold', 'bank_off_gold'
    ];

}
