<?php

namespace App\Models;

use App\Models\Observers\RefreshBaseHotConfigureCacheObserver;
use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BaseHotConfigure.
 *
 * @package namespace App\Models;
 */
class BaseHotConfigure extends Model implements Transformable
{
    //自身關係
    const RELATION_SELF = 'self';
    //全部的設定快取
    const CACHE_KEY = 'BaseHotConfigure';
    //每一筆規則上次的指標數值 prefix
    const PRE_METRIC_CACHE_KEY_PREFIX = 'metric:';
    const PRE_METRIC_LASTEST_TIME_CACHE_KEY_PREFIX = 'metric_lastest_time:';
    const PRE_CHECK_POINT_METRIC_CACHE_KEY_PREFIX = 'metric_check_point:';
    const RUNNING_REWARD_CACHE_KEY = 'running_reward:';
    use TransformableTrait;

    //熱度計算類型
    const TYPE_FIX = '1'; //固定
    const TYPE_DYNAMIC = '2'; //动态
    const TYPE_ITEM = '3'; //道具
    const TYPE_ACTIVITY = '4'; //活动
    //單位類型
    const UNIT_TYPE_PERSON = 'person'; //用人數計算
    const UNIT_TYPE_GOLD = 'gold'; //用當前幣值
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'model_name', 'metric_column', 'unit', 'range', 'name', 'start_at', 'finish_at'];

    public function getMetricAttribute($value)
    {
        $result = json_decode($value, true);
        if (json_last_error()) {
            $result = [];
        }
        return $result;
    }

    protected static function boot()
    {
        parent::boot();
        static::observe(RefreshBaseHotConfigureCacheObserver::class);
    }
    /**
     * 取得最近的檢查點的時間數值
     *
     * @param    int                   $configId 主鍵ID
     *
     * @return   int                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T13:18:22+0800
     */
    public static function getTheLastestCheckPointMetricValue($roomId, $configId)
    {
        $lastestCheckPointCacheKey = self::PRE_CHECK_POINT_METRIC_CACHE_KEY_PREFIX . $roomId . ':' . $configId;
        $value = \Cache::get($lastestCheckPointCacheKey);
        if (!$value) {
            $value = 0;
        }
        return $value;
    }

    /**
     * 取得正在執行的獎勵熱度
     *
     * @param    [type]                   $config [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T16:21:23+0800
     */
    public static function getRunningReward($roomId, $configId)
    {
        $cacheKey = self::RUNNING_REWARD_CACHE_KEY . $roomId . ':' . $configId;
        $reward = \Cache::get($cacheKey);
        if (!$reward) {
            //找不到的話，回空陣列
            $reward = [];
        }
        return $reward;
    }

    /**
     * 設定正在獲得的獎勵熱度
     *
     * @param    [type]                   $config [description]
     * @param    [type]                   $reward [description]
     * @param    [type]                   $time   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T16:21:58+0800
     */
    public static function setRunningReward($roomId, $configId, $reward)
    {
        $cacheKey = self::RUNNING_REWARD_CACHE_KEY . $roomId . ':' . $configId;
        \Cache::put($cacheKey, $reward, $reward['addTime']); //每筆記錄，根據獎勵設定的時間
    }
    /**
     * 設定上次的檢查點指標數值
     *
     * @param    int                   $configId 主鍵ID
     *
     * @return   int                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T13:18:22+0800
     */
    public static function setTheLastestCheckPointMetric($roomId, $configId, $metricValue)
    {
        $cacheKey = self::PRE_CHECK_POINT_METRIC_CACHE_KEY_PREFIX . $roomId . ':' . $configId;

        \Cache::put($cacheKey, $metricValue, 30); //每筆記錄，活30分鐘
    }

    /**
     * 設定每十秒的指標數值
     *
     * @param    int                   $configId 主鍵ID
     *
     * @return   int                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T13:18:22+0800
     */
    public static function setMetricValue($roomId, $configId, $value, $now)
    {
        $cacheKey = self::getMetricValueCacheKey($roomId, $configId, $now);
        \Cache::put($cacheKey, $value, 30); //每筆記錄，活30分鐘
    }
    /**
     * 取得每個時間點的指標的快取
     *
     * @param    [type]                   $configId [description]
     * @param    [type]                   $now      [description]
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T17:12:02+0800
     */
    public static function getMetricValueCacheKey($roomId, $configId, $now)
    {
        return self::PRE_METRIC_CACHE_KEY_PREFIX . $roomId . ':' . $configId . '@' . $now;
    }
    public static function getLastestMetricTime($configId, $value)
    {
        $cacheKey = self::PRE_METRIC_LASTEST_TIME_CACHE_KEY_PREFIX . $configId;

    }
    /**
     * 取得最近的時間(如果開房間時間，沒有超過十分鐘)
     *
     * @param    [type]                   $configId [description]
     * @param    [type]                   $now      [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T15:26:05+0800
     */
    public static function setLastestMetricTime($configId, $now)
    {
        $cacheKey = self::PRE_METRIC_LASTEST_TIME_CACHE_KEY_PREFIX . $configId;
        \Cache::put($cacheKey, $now, 30);
    }
    /**
     * 根據單位返回對應的指標增加數量
     *
     * @param    [type]                   $metric [description]
     * @param    [type]                   $config [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T19:03:46+0800
     */
    public static function getMetricValueByUnitType($metric, $config)
    {
        $unit = $config['unit'] ?? 1;
        return round($metric / $unit);
    }

    public function getRangeAttribute($value)
    {
        $range = json_decode($value, true);
        if (json_last_error()) {
            $range = [];
        }
        return $range;
    }
}
