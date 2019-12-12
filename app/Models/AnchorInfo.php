<?php

namespace App\Models;

use App\Models\AnchorAdversting;
use App\Models\BaseModel as Model;
use App\Models\Observers\AnchorInfoObserver;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @SWG\Definition(
 *      definition="AnchorInfo",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="流水号"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          type="integer",
 *          format="int32",
 *          description="用户id"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          type="integer",
 *          format="int32",
 *          description="经纪公司id"
 *      ),
 *      @SWG\Property(
 *          property="manager_id",
 *          type="integer",
 *          format="int32",
 *          description="经纪人id"
 *      ),
 *      @SWG\Property(
 *          property="front_cover",
 *          type="string",
 *          description="主播封面"
 *      ),
 *      @SWG\Property(
 *          property="can_live",
 *          type="integer",
 *          format="int32",
 *          description="是否可以直撥"
 *      ),
 * )
 */
/**
 * Class AnchorInfo.
 *
 * @package namespace App\Models;
 */
class AnchorInfo extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'anchor_info';
    const CAN_LIVE_YES = 1;
    const CAN_LIVE_NO = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'company_id', 'manager_id', 'front_cover', 'can_live', 'can_live_history'];

    public static function boot()
    {
        parent::boot();
        static::observe(AnchorInfoObserver::class);
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Manager', 'company_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function live()
    {
        return $this->hasMany('App\Models\LiveRoom', 'user_id', 'user_id');
    }
    public function real_name_verify()
    {
        return $this->hasOne(RealNameVerify::class, 'user_id', 'user_id');
    }

    public function realNameVerify()
    {
        return $this->hasOne(RealNameVerify::class, 'user_id', 'user_id');
    }
    public function anchorAdverstings()
    {
        return $this->hasMany(AnchorAdversting::class, 'user_id', 'user_id');
    }
    public static function getEmptyModel()
    {
        $default = [
            'level' => 1,
            'password' => env('DEFAULT_PASSWORD'),
            'gold' => 0,
            'manager' => 0,
            'company_id' => 0,
            'company_id' => 0,
            'real_name' => '', 'no' => '',
            'cellphone' => '',
            'alipay_account' => '',
            'photo' => '',
        ];
        return $default;
    }

    protected function setCanLiveAttribute($value)
    {
        if ($this->getOriginal()) {
            $originHistory = $this->getOriginal()['can_live_history'] ?? '[]';
        } else {
            $originHistory = '[]';
        }
        $originHistory = json_decode($originHistory, true);
        if (json_last_error()) {
            $originHistory = [];
        }
        $insert = [
            'time' => date("Y-m-d H:i:s", time()),
            'source' => $value,
        ];
        $originHistory[] = $insert;
        $this->attributes['can_live'] = $value;
        $this->attributes['can_live_history'] = json_encode($originHistory);
        return $this;
    }

    public function getFrontCoverAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }

    public function syncAnchorAdversting($adversting = [])
    {
        //先取得当前有的关联
        $this->load('anchorAdverstings');
        $currentAnchorAdverstings = $this->anchorAdverstings->pluck('anchor_adv_type')->toArray();
        $add = array_diff($adversting, $currentAnchorAdverstings);
        $delete = array_diff($currentAnchorAdverstings, $adversting);
        //新增
        foreach ($add as $key => $value) {
            $anchorAdverstingModel = new AnchorAdversting();
            $anchorAdverstingModel->user_id = $this->user_id;
            $anchorAdverstingModel->anchor_adv_type = $value;
            $anchorAdverstingModel->save();
        }
        //删除
        if ($delete) {
            $anchorAdverstingModel = AnchorAdversting::where('user_id', $this->user_id)->whereIn('anchor_adv_type', $delete)->delete();
        }

    }
}
