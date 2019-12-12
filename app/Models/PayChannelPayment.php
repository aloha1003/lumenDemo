<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @SWG\Definition(
 *      definition="储值方式",
 *      @SWG\Property(
 *          property="pay_id",
 *          type="integer",
 *          format="int32",
 *          description="储值流水号"
 *      ),
 *      @SWG\Property(
 *          property="alias",
 *          type="string",
 *          description="前端显示储值方式名称"
 *      ),
 *      @SWG\Property(
 *          property="pay_channels_slug",
 *          type="string",
 *          description="支付渠道名称"
 *      ),
 *      @SWG\Property(
 *          property="pay_type",
 *          type="string",
 *          description="支付方式"
 *      ),
 *      @SWG\Property(
 *          property="order_amounts",
 *          type="array",
 *          description="该种支付方式支援的储值面额列表",
 *          @SWG\Items(
 *                      type="integer",
 *                  )
 *      ),
 *      @SWG\Property(
 *          property="icon",
 *          type="string",
 *          description="支付方式图示连结",
 *      ),
 *      @SWG\Property(
 *          property="custom_amount",
 *           type="enum",
 *          enum={"1", "0"},
 *          description="是否支援客户自行输入金额, 1: 表示可以，0 表示不行"
 *      )
 * )
 */
/**
 * Class PayChannelPayment.
 *
 * @package namespace App\Models;
 */
class PayChannelPayment extends Model implements Transformable
{
    use SoftDeletes;
    use TransformableTrait;
    use SortableTrait;
    // 是否启用
    const AVAILABLE_ENABLE = 1;
    const AVAILABLE_DISABLE = 0;
    // 是否允许 输入客制化字串
    const CUSTOMAMOUNT_YES = 1;
    const CUSTOMAMOUNT_NO = 0;
    // 是否是高品质
    const HIGH_QUALITY_YES = 1;
    const HIGH_QUALITY_NO = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'pay_channels_slug', 'fee', 'alias', 'pay_type', 'available', 'rank', 'order_amounts', 'custom_amount', 'high_quality', 'comment'];
    public $sortable = [
        'order_column_name' => 'rank',
        'sort_when_creating' => true,
    ];
    // protected $appends = ['call_back_type'];
    public function channel()
    {
        return $this->hasOne('App\Models\PayChannel', 'pay_channels_slug', 'slug');
    }

    // public function getCallBackTypeAttribute($value)
    // {
    //     if ($this->pay_channels_slug && $this->pay_type) {
    //         return payment($this->pay_channels_slug, $this->pay_type)->getCallBackType();
    //     } else {
    //         return '';
    //     }
    // }
}
