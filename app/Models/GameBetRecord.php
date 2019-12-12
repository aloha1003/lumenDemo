<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GameBetRecord.
 *
 * @package namespace App\Models;
 */
class GameBetRecord extends Model implements Transformable
{
    use TransformableTrait;

    const STATUS_WAIT = 0;
    const STATUS_WIN = 1;
    const STATUS_LOSE = 2;

    public $table = 'game_bet_record';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'channel',
        'room_id',
        'bet_gold',
        'game_slug',
        'game_round',
        'status',
        'win_gold',
    ];
}
