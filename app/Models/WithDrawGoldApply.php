<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\WithDrawGoldObserver;
use App\Models\RealNameVerify;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WithDrawGoldApply.
 *
 * @package namespace App\Models;
 */
/**
 * @SWG\Definition(
 *      definition="提现记录",
 *       description="提现记录",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="流水號"
 *      ),
 *      @SWG\Property(
 *          property="gold",
 *          type="integer",
 *          format="int32",
 *          description="提现金额"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          type="integer",
 *          format="int32",
 *          enum={0, 1, 2, 3, 4},
 *          description="审核状态, 0: 未审核，1: 审核通过，2:审核进行中，3:审核驳回, 4:提现申请取消"
 *      ),
 *      @SWG\Property(
 *          property="status_title",
 *          type="integer",
 *          format="string",
 *           example="尚未审核",
 *          description="审核状态-中文名稱"
 *      ),
 *      @SWG\Property(
 *          property="appeal_status",
 *          type="integer",
 *          format="int32",
 *          description="申訴状态 0: 可申訴, 1:申訴中"
 *      ),
 *      @SWG\Property(
 *          property="account",
 *          type="string",
 *          description="提现帐号",
 *      ),
 *      @SWG\Property(
 *          property="payment_channels_slug",
 *          type="string",
 *          description="提现帐号类型",
 *          example="ali",
 *      ),
 *      @SWG\Property(
 *          property="alias",
 *          type="string",
 *          description="提现類型別名",
 *          example="支付宝",
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          type="integer",
 *          description="用户ID"
 *      ),
 *      @SWG\Property(
 *          property="profit",
 *          type="integer",
 *          description="实际提领金额"
 *      ),
 *      @SWG\Property(
 *          property="cost",
 *          type="number",
 *          description="手续费"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          type="string",
 *          description="建立日期",
 *          example="2019-10-25 12:49:05",
 *      )
 * )
 */
class WithDrawGoldApply extends Model implements Transformable
{
    use TransformableTrait;

    //审核状态 : 0: 未审核，1: 审核通过，2:审核进行中，3:审核驳回
    //正常的审核通过流程为 未审核 -> 审核进行中 -> 审核通过
    //正常的审核驳回流程为 未审核 -> 审核进行中 -> 审核驳回
    //当状态 不是 未审核，不得取消
    const STATUS_NO = 0;
    const STATUS_PASS = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_REJECT = 3;
    const STATUS_CANCEL = 4;

    //申訴状态
    const APPEAL_STATUS_CAN = 0; // 可提出申訴
    const APPEAL_STATUS_WAIT = 1; // 申訴中

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fromSourceUserId', 'gold', 'status', 'appeal_status', 'account', 'payment_channels_slug', 'user_id', 'profit', 'fee', 'fee_type', 'admin_id', 'comment', 'transaction_no'];

    protected $appends = ['status_title'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function real_name_verify()
    {
        return $this->hasOne(RealNameVerify::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(config('admin.database.users_model'), 'admin_id');
    }

    public function payment_channel()
    {
        return $this->belongsTo('App\Models\PaymentChannel', 'payment_channels_slug', 'slug');
    }

    public static function boot()
    {
        parent::boot();
        static::observe(WithDrawGoldObserver::class);
    }

    /**
     * 设定金币，必为正数
     *
     * @param    [type]                   $value [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-11T17:25:07+0800
     */
    protected function setGoldAttribute($value)
    {
        $this->attributes['gold'] = abs($value);
        return $this;
    }

    // protected function getPaymentChannelsSlugTitleAttribute($value)
    // {
    //     return __('paymentChannel.fee_type_list.' . $this->payment_channels_slug);
    // }

    protected function getStatusTitleAttribute($value)
    {

        return __('withDrawGoldApply.status_list.' . $this->status);
    }

    /**
     * 建立订单编号
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T14:22:40+0800
     */
    public function generateTransactionNo()
    {
        $prefix = env('TOPUP_ORDER_NO_PREFIX', 'GYL') . 'O';
        $time = time();
        $date = date("YmdHis", $time);
        $milliseconds = round(microtime(true) * 1000) - $time * 1000;
        $no = $prefix . $date . $milliseconds . strtoupper(substr(uniqid(), 9));
        return $no;
    }
}
