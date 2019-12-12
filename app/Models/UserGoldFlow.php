<?php

namespace App\Models;

use App\Contracts\PlatformMapAble;
use App\Contracts\SourceModelAble;
use App\Traits\PlatFormTrait;
use App\Traits\SourceModelTrait;
use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserGoldFlow.
 *
 * @package namespace App\Models;
 */
class UserGoldFlow extends Model implements Transformable, PlatformMapAble, SourceModelAble
{
    protected $isLog = false;

    use TransformableTrait;
    use PlatFormTrait;
    use SourceModelTrait;
    public $table = 'user_gold_flow';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'gold_origin',
        'gold_operation',
        'gold_remain',
        'source_model_name',
        'source_model_primary_key_column',
        'source_model_primary_key_id',
        'amount',
        'from_which_platform',
    ];
    protected $appends = ['transaction_type', 'source_model_name_title', 'from_which_platform_title'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 取得平台用户
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-25T13:11:29+0800
     */
    public function platform_user()
    {
        return $this->getPlatformMapRelation($this->from_which_platform);
    }

    /**
     * 取得交易类型
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-17T11:54:05+0800
     */
    public function getTransactionTypeAttribute($value)
    {
        if ($this->gold_operation > 0) {
            return __('common.transactionTypeList.in');
        } else {
            return __('common.transactionTypeList.out');
        }
    }

    /**
     * 取得栏位名称
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-17T11:57:16+0800
     */
    public function getSourceModelNameTitleAttribute($value)
    {
        return $this->getModelName($this->source_model_name);
    }

    /**
     * 取得栏位名称
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-17T11:57:16+0800
     */

    public function getFromWhichPlatformTitleAttribute($value)
    {
        return $this->platformLabel($this->from_which_platform);
    }
}
