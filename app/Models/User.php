<?php

namespace App\Models;

use App\Models\AnchorInfo;
use App\Models\Manager as ManagerModel;
use App\Models\Observers\CleanCacheObserver;
use App\Models\Observers\ModelLogObserver;
use App\Models\Observers\UserGoldFlowObserver;
use App\Models\Observers\UserGoldObserver;
use App\Models\Observers\UserInfoObserver;
use App\Models\Observers\UserObserver;
use App\Models\RealNameVerify;
use App\Models\SpecialAccount;
use App\Models\UserConfig;
use App\Models\UserFollow;
use App\Models\UserLoginRecord;
use App\Models\UserTopupOrder;
use App\Model\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @SWG\Definition(
 *      definition="User",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="直播主的用戶id"
 *      ),
 *      @SWG\Property(
 *          property="user_type_id",
 *          type="integer",
 *          format="int32",
 *          description="用户类型，0:一般用户，1:主播，2:代理"
 *      ),
 *      @SWG\Property(
 *          property="cellphone",
 *          type="string",
 *          description="手机"
 *      ),
 *      @SWG\Property(
 *          property="nickname",
 *          type="string",
 *          description="昵称"
 *      ),
 *      @SWG\Property(
 *          property="sex",
 *          type="int",
 *          description="性别1:男，0:女"
 *      ),
 *      @SWG\Property(
 *          property="birthday",
 *          type="string",
 *          description="生日"
 *      ),
 *     @SWG\Property(
 *          property="avatar",
 *          type="string",
 *          description="头像连结"
 *      ),
 *     @SWG\Property(
 *          property="sign",
 *          type="string",
 *          description="签名档"
 *      ),
 *     @SWG\Property(
 *          property="level",
 *          type="string",
 *          description="经验值"
 *     ),
 *     @SWG\Property(
 *          property="gold",
 *          type="integer",
 *          description="金币"
 *     ),
 *     @SWG\Property(
 *          property="pretty_id",
 *          type="integer",
 *          description="靓号"
 *     )
 * )
 */
/**
 * Class User.
 *
 * @package namespace App\Models;
 */
class User extends Authenticatable implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    public $goldUpdateSourceModel = null;

    protected $dates = ['deleted_at'];
    protected $isLog = true;
    //性别
    const SEX_UNKNOW = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    const USER_TYPE_NORMAL = 0; //
    const USER_TYPE_ANCHOR = 1; //
    const USER_TYPE_AGENT = 2; //
    //是否为主播
    const IS_AGENT_NO = 0;
    const IS_AGENT_YES = 1;
    //是否可提现
    const IS_WITHDRAW_NO = 0;
    const IS_WITHDRAW_YES = 1;
    public $realGold = null;
    public $realGoldWithoutDiff = null;
    public $goldDiff = 0;

    public $table = 'user';
    public $fillable = [
        'last_login_at',
        'continue_login_days',
        'user_type_id',
        'cellphone',
        'nickname',
        'sex',
        'birthday',
        'avatar',
        'sign',
        'level',
        'gold',
        'manager_id',
        'accumulation_gift_gold_receive',
        'accumulation_gift_gold_given',
        'id',
        'register_channel',
        'register_uuid',
        'register_device_type',
        'register_os_version',
        'real_withdraw_gold',
    ];

    public $appends = ['is_anchor', 'currentLevel', 'pretty_id', 'user_type', 'is_can_withdraw'];

    protected $attributes = [
        'gold' => 0,
    ];

    public function auth()
    {
        return $this->hasOne('App\Models\UserAuth', 'user_id', 'id');
    }

    public function follows()
    {
        return $this->hasMany(UserFollow::class, 'user_id', 'id');
    }
    /**
     * 取得性别说明
     *
     * @return   array
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-19T14:43:36+0800
     */
    public function getSexTitle()
    {
        return __('user.sex_title');
    }

    /**
     * 取得使用者类型
     *
     * @return   array
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-19T14:44:22+0800
     */
    public function getUserTypeTitle()
    {
        return __('user.user_type_title');
    }

    public static function boot()
    {
        parent::boot();
        //写入修改log
        static::observe(ModelLogObserver::class);
        //写入金币累积变动
        static::observe(UserGoldObserver::class);
        //写入金币流水记录
        static::observe(UserGoldFlowObserver::class);
        //初始化 用户子表 user_config 建立
        static::observe(UserObserver::class);
        // 更新用户资料快取
        static::observe(UserInfoObserver::class);
        //清除 repository 的查询快取
        static::observe(CleanCacheObserver::class);
    }

    public function getIsLog()
    {
        return $this->isLog;
    }

    public static function getTableName()
    {
        return with(new static )->getTable();
    }

    public function getIsAnchorAttribute($value)
    {
        if ($this->user_type_id == self::USER_TYPE_ANCHOR) {
            return 1;
        } else {
            return 0;
        }
    }

    public function manager()
    {
        return $this->hasOne('App\Models\Manager', 'manager_id', 'id');
    }

    public function real_name_verify()
    {
        return $this->hasOne(RealNameVerify::class, 'user_id', 'id');
    }

    public function anchor()
    {
        return $this->hasOne(AnchorInfo::class, 'user_id', 'id');
    }

    public function loginRecord()
    {
        return $this->hasMany(UserLoginRecord::class, 'user_id', 'id');
    }

    public function theLastestloginRecord()
    {
        return $this->hasOne(UserLoginRecord::class, 'user_id', 'id')
            ->select('ip', \DB::raw('max(id) as max_id'), 'user_id')
            ->groupBY('user_id')
        ;
    }

    public function userConfig()
    {
        return $this->hasOne(UserConfig::class, 'user_id', 'id');
    }

    public function topupOrders()
    {
        return $this->hasMany(UserTopupOrder::class, 'user_id');
    }

    public function getCurrentLevelAttribute($value)
    {
        return getCurrentLevelByExp($this->level);
    }

    public function getCurrentExpAttribute()
    {
        $lv = getCurrentLevelByExp($this->level);
        $exp = getWhichExpByLevel($lv);
        return $this->level - $exp;
    }

    public function getGoldUpdateSourceModel()
    {
        return $this->goldUpdateSourceModel;
    }

    public function getPrettyIdAttribute($value)
    {
        $prettyId = \Cache::get(SpecialAccount::prettyIdKey($this->id));
        if ($prettyId) {
            return $prettyId;
        } else {
            return $this->id;
        }
    }

    public function getUserTypeAttribute($value)
    {

        return __('user.user_type_title')[$this->user_type_id] ?? "";
    }

    public function getAvatarAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }
    public function getGoldCacheKey($userId)
    {
        return config('cache.prefix') . ':user_gold_info:' . $userId . ':gold';
    }

    public function getGoldDiffCacheKey($userId)
    {
        return config('cache.prefix') . ':user_gold_info:' . $userId . ':diff';
    }

    /**
     * 取得gold with diff
     */
    public function getGoldCacheAttribute($value)
    {
        if ($this->realGold == null) {
            $this->realGold = $this->getRealGold($this->id);
        }
        return $this->realGold;
    }

    /**
     * 取得gold
     */
    // public function getGoldAttribute($value)
    // {
    //     //修正如果是新注册的人，就直接返回原值就好
    //     if (!$this->id) {
    //         return $value;
    //     }
    //     if ($this->realGoldWithoutDiff != null) {
    //         return $this->realGoldWithoutDiff;
    //     }

    //     $userId = $this->id;
    //     $goldCacheKey = $this->getGoldCacheKey($userId);
    //     $redis = \Cache::store('redis')->getRedis();
    //     $goldData = $redis->get($goldCacheKey);
    //     if ($goldData == null) {
    //         $userData = \DB::table('user')->where('id', $userId)->first();
    //         $goldData = $userData->gold;
    //         $redis->set($goldCacheKey, $goldData);
    //     }
    //     $this->realGoldWithoutDiff = $goldData;

    //     return $this->realGoldWithoutDiff;
    // }

    /**
     * 增加金幣
     */
    public function addGold($gold, $sourceModel, $updateCacheOnly = false, $updateCacheOnlyCallback = null)
    {
        $userId = $this->id;

        if ($updateCacheOnly) {

            // 寫入快取
            $fieldKey = uniqid($userId);
            $cacheKey = $this->getGoldDiffCacheKey($userId);
            $redis = \Cache::store('redis')->getRedis();
            $redis->hset($cacheKey, $fieldKey, $gold);

            if (is_callable($updateCacheOnlyCallback)) {
                $updateCacheOnlyCallback($cacheKey, $fieldKey);
            }
            return;
        }

        $this->addRealGold($userId, $gold, $sourceModel);
    }

    public function setRealGold($userId, $gold, $sourceModel)
    {
        // 更新資料庫
        $this->goldUpdateSourceModel = $sourceModel;
        $this->gold = $gold;
        $this->save();

        $redis = \Cache::store('redis')->getRedis();
        $goldCacheKey = $this->getGoldCacheKey($userId);
        $redis->set($goldCacheKey, $gold);
    }

    public function addRealGold($userId, $gold, $sourceModel)
    {
        $userModel = User::where(['id' => $userId])->get()->first();

        $newGold = $userModel->gold + $gold;
        $this->goldDiff = $gold;

        $this->setRealGold($userId, $newGold, $sourceModel);
    }

    protected function getRealGold($userId)
    {
        $goldCacheKey = $this->getGoldCacheKey($userId);
        $diffCacheKey = $this->getGoldDiffCacheKey($userId);

        $redis = \Cache::store('redis')->getRedis();
        $diffData = $redis->hgetall($diffCacheKey);
        $goldData = $redis->get($goldCacheKey);

        if ($goldData == null) {
            $userModel = User::where(['id' => $userId])->get()->first();
            $goldData = $userModel->gold;
            $redis->set($goldCacheKey, $goldData);
        }

        if ($diffData == null || $diffData == []) {
            return floor($goldData);
        }

        $resultGold = $goldData;
        foreach ($diffData as $key => $diff) {
            $resultGold += $diff;
        }

        return floor($resultGold);
    }

    public function scopeTopupAmount($query, $range = [])
    {
        $topupTable = app(UserTopupOrder::class)->getTableName();
        if (!$range) {
            $range = [0, 100000];
        }
        $query->whereIn('user.id', function ($query) use ($topupTable, $range) {
            $query->select('user_id')
                ->from($topupTable)
                ->where('pay_step', UserTopupOrder::PAY_STEP_SUCCESS)
                ->groupBY('user_id')
            // ->havingRaw(\DB::raw('SUM(amount)'), '>=', $range[0]);
                ->havingRaw(' SUM(amount) >= ' . $range[0] . ' AND  SUM(amount) <=' . $range[1]);
        })->addSelect(\DB::raw('SUM(' . $topupTable . '.amount) as amount_sum'), \DB::raw('pay_at as order_pay_at'))
            ->where($topupTable . '.pay_step', UserTopupOrder::PAY_STEP_SUCCESS)
            ->join($topupTable, 'user.id', '=', $topupTable . '.user_id')
            ->groupBY($topupTable . '.user_id')
        ;
    }
    /**
     * 取得指定日期的登入人数
     *
     * @param    [type]                   $date [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-13T10:33:39+0800
     */
    public function scopeLoginAccount($query, $date)
    {
        $record = $query->where([[\DB::raw('date(last_login_at)'), '=', $date]])
            ->select(\DB::raw('count(1) as count'))
            ->get()
            ->first()
            ->only('count')
        ;
        return $record['count'] ?? 0;
    }
    //是否可以提现，如果没有经纪人，或是经纪人ID 是 ManagerModel::NONE_COMPANY_ID、ManagerModel::NONE_MANAGER_ID 就可以提现
    public function getIsCanWithdrawAttribute($value)
    {
        $isCanWithdraw = 0;
        if (!$this->manager_id) {
            $isCanWithdraw = 1;
        } else {
            switch ($this->manager_id) {
                case ManagerModel::NONE_COMPANY_ID:
                case ManagerModel::NONE_MANAGER_ID:
                    $isCanWithdraw = 1;
                    break;
                default:
                    $isCanWithdraw = 0;
                    break;
            }
        }
        return $isCanWithdraw;
    }

    /**
     * 是否可以提现
     *
     * @return   boolean                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-22T10:25:35+0800
     */
    public function isCanDraw()
    {
        return $this->is_can_withdraw == self::IS_WITHDRAW_YES;
    }

}
