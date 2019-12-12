<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\UserTopupOrderObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @SWG\Definition(
 *      definition="充值返回资料",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="订单流水号"
 *      ),
 *      @SWG\Property(
 *          property="transaction_no",
 *          type="string",
 *          description="充值订单编号，前端可以显示给用户，方便用户反馈问题"
 *      ),
 *      @SWG\Property(
 *          property="call_back_type",
 *          type="enum",
 *          enum={"qrcode", "link"},
 *          description="支付返回网址的类型, qrcode:前端需显示QRCODE，link:一般超连结(一般网银无扫码，送出时有可能需要提供网银返回连结)"
 *      ),
 *      @SWG\Property(
 *          property="link",
 *          type="string",
 *          description="支付返回网址"
 *      )
 * )
 */
/**
 * @SWG\Definition(
 *      definition="充值订单资料",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="订单流水号"
 *      ),
 *      @SWG\Property(
 *          property="transaction_no",
 *          type="string",
 *          description="订单编号"
 *      ),
 *     @SWG\Property(
 *         property="pay_at",
 *         description="交易完成日期",
 *         type="date-time"
 *     ),
 *     @SWG\Property(
 *         property="created_at",
 *         description="交易建立日期",
 *         type="date-time"
 *     ),
 *     @SWG\Property(
 *         property="pay_type",
 *         description="交易方式 ALI:支付宝,WEIXIN:微信,IBK:网银,UNION:銀聯,QQ:QQ钱包,CLD:云闪付 ",
 *         enum={"ALI","WEIXIN","IBK","UNION","QQ","CLD"},
 *         type="enum"
 *     ),
 *     @SWG\Property(
 *         property="gold",
 *         description="该笔交易取得的金币数量",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="amount",
 *         description="该笔交易的金额'",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="pay_step_title",
 *         description="订单阶段",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="appeal_status",
 *         description="是否可提出申訴",
 *         type="integer"
 *     )
 * )
 */
/**
 * Class UserTopupOrder.
 *
 * @package namespace App\Models;
 */
class UserTopupOrder extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    protected $isLog = false;
    //订单阶段
    const PAY_STEP_INIT = 'INIT'; //订单初始化
    const PAY_STEP_THIRD_ERR = 'THIRD_ERR'; //申请第三方支付失败
    const PAY_STEP_THIRD_CALLBACK_ERR = 'THIRD_CALLBACK_ERR'; //第三方回调回写失败
    const PAY_STEP_PEND = 'PEND'; //订单等待客户付款或是等待第三方回调
    const PAY_STEP_SUCCESS = 'SUCCESS'; //订单交易完成
    const PAY_STEP_ABORT = 'ABORT'; //订单逾时
    const PAY_STEP_CANCEL = 'CANCEL'; //订单取消

    //回调通知状态
    const NOTIFY_STATUS_PASS = 1; //成功
    const NOTIFY_STATUS_NO = 0; //尚未回调
    const NOTIFY_STATUS_FAIL = 2; //异步失败

    //申訴状态
    const APPEAL_STATUS_CAN = 0; // 可提出申訴
    const APPEAL_STATUS_WAIT = 1; // 申訴中

    public $payload = false;
    public $payUrl = false;
    public $excepts = ['payload', 'payUrl'];
    public $appends = ['pay_step_title'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['transaction_no', 'pay_transaction_no', 'pay_step', 'notify_status', 'appeal_status', 'user_id', 'pay_channels_slug', 'pay_channel_payments_pay_type', 'fee', 'cost', 'amount', 'profit', 'gold', 'pay_detail', 'pay_at', 'from_which_platform', 'user_register_channel'];
    public static function boot()
    {
        parent::boot();
        static::observe(UserTopupOrderObserver::class);
    }

    public function getPayAtAttribute($value)
    {
        return ($value) ?? '';
    }

    public function getPayTransactionNoAttribute($value)
    {
        return ($value) ?? '';
    }

    public function getCreatedAtAttribute($value)
    {
        return $value;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getGoldAttribute($value)
    {
        return intval($value);
    }

    public function getAmountAttribute($value)
    {
        return intval($value);
    }
    public function getPayStepTitleAttribute()
    {
        return __('userTopupOrder.pay_step_list_front')[$this->pay_step] ?? '';
    }
    public function payStepList()
    {
        return __('userTopupOrder.pay_step_list');
    }
    /**
     * 订单变更记录
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T09:20:45+0800
     */
    public function logs()
    {
        return $this->hasMany('App\Models\UserTopupOrderLog', 'transaction_no', 'transaction_no');
    }
}
