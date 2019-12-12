<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\LiveRoomObserver;
use App\Models\Observers\UserInfoObserver;
use App\Services\LiveRoom as LiveRoomService;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class LiveRoom.
 *
 * @package namespace App\Models;
 */

/**
 * @SWG\Definition(
 *      definition="LiveRoom",
 *      required={"id", "name", "route"},
 *      @SWG\Property(
 *          property="user_id",
 *          type="integer",
 *          format="int32",
 *          description="直播主的用戶id"
 *      ),
 *
 *      @SWG\Property(
 *          property="anchor_info",
 *          type="object",
 *          description="主播资料",
 *          @SWG\Property(property="front_cove", type="string", example=1,description="主播封面"),
 *      ),
 *      @SWG\Property(
 *          property="game_slug",
 *          type="string",
 *          description="游戏唯一识别码"
 *      ),
 *      @SWG\Property(
 *          property="watch_number",
 *          type="integer",
 *          description="觀看人數"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          type="string",
 *          description="Hash過後的密碼，密碼不為空，表示為私密房"
 *      ),
 *      @SWG\Property(
 *          property="hot_value",
 *          type="integer",
 *          description="直播間的熱度"
 *      ),
 *      @SWG\Property(
 *          property="gift_value",
 *          type="integer",
 *          description="總送禮價值"
 *      ),
 *      @SWG\Property(
 *          property="stream_url",
 *          type="integer",
 *          description="推播网址"
 *      ),
 *      @SWG\Property(
 *          property="is_follow",
 *          type="boolean",
 *          description="是否追随"
 *      ),
 *      @SWG\Property(
 *          property="rank",
 *          type="integer",
 *          description="熱門直播間排名",
 *          example=1
 *      ),
 *      @SWG\Property(property="user", type="object", ref="#/definitions/User"),
 *      @SWG\Property(property="anchor", type="object", ref="#/definitions/AnchorInfo"),
 *      @SWG\Property(property="game", type="object", ref="#/definitions/Game")
 * )
 */
class LiveRoom extends Model implements Transformable
{
    use TransformableTrait;
    // 状态类型
    const STATUS_LIVE = 1;
    const STATUS_STOP = 0;
    const STATUS_FORBIDDEN = 2;
    //房间类型
    const ROOM_TYPE_PUBLIC = 1;
    const ROOM_TYPE_PRIVATE = 2;
    protected $appends = ['watch_number', 'stream_url'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'user_id', 'game_slug', 'real_user_number', 'hot_value', 'total_receive_gold', 'total_real_receive_gold', 'fans_before_open', 'fans_after_open', 'password', 'stream_urls', 'start_at', 'leave_at', 'duration'];

    public static function getStatus()
    {
        return __('liveRoom.statusList');
    }

    public function anchorInfo()
    {
        return $this->belongsTo('App\Models\AnchorInfo', 'user_id', 'user_id');
    }

    public function manager()
    {
        return $this->HasManyThrough(
            'App\Models\Manager',
            'App\Models\AnchorInfo',
            'user_id', // Local key on dog_owner table...
            'id', // Local key on dogs table...
            'user_id', // Foreign key on walks table...
            'manager_id' // Foreign key on dog_owner table...
        );

    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function game()
    {
        return $this->hasOne('App\Models\Game', 'slug', 'game_slug');
    }
    /**
     * 遊戲下注資料
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T11:45:28+0800
     */
    public function gameBetRecords()
    {
        return $this->hasMany('App\Models\GameBetRecord', 'room_id');
    }

    /**
     * 送禮記錄
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T11:46:28+0800
     */
    public function giftTransactionOrders()
    {
        return $this->hasMany('App\Models\GiftTransactionOrder', 'room_id');
    }

    public function getWatchNumberAttribute($value)
    {
        return 0;
    }

    public function getStreamUrlAttribute($value)
    {
        return '/api/live/' . $this->id . '/enter';
    }

    public function getStreamUrlsAttribute($value)
    {
        $urls = json_decode($value, true);
        if (json_last_error()) {
            $urls = ['push' => '', 'pull' => []];
        }
        return $urls;
    }

    protected function calculateDuration()
    {
        if ($this->start_at) {
            $start = strtotime($this->start_at);
        } else {
            $start = time();
        }
        if ($this->leave_at) {
            $leave = strtotime($this->leave_at);
        } else {
            $leave = time();
        }
        $period = $leave - $start;
        return $period;

    }

    public static function boot()
    {
        parent::boot();
        static::observe(LiveRoomObserver::class);
        static::observe(UserInfoObserver::class);
    }

    public function scopeOfType($query, $type)
    {
        switch ($type) {
            case self::ROOM_TYPE_PUBLIC:
                $query->where('password', '=', '');
                break;
            case self::ROOM_TYPE_PRIVATE:
                $query->where('password', '!=', '');
                break;
            default:
                # code...
                break;
        }
        return $query;
    }
    /**
     * 取得Md5後的假密碼
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T17:38:32+0800
     */
    public function getPasswordAttribute($value)
    {
        return ($value) ? md5($value . now()) : '';
    }

    public function getHotValueAttribute($value)
    {
        $key = LiveRoomService::getHotInfoCacheKey($this->id);
        $value = \Cache::get($key) ?? $value;
        return $value;
    }
}
