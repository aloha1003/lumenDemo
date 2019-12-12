<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PaymentChannel.
 *
 * @package namespace App\Models;
 */
class PaymentChannel extends Model implements Transformable
{
    use TransformableTrait;
    //手续费类型
    const FEE_TYPE_FIX = 'fix';
    const FEE_TYPE_RATE = 'rate';
    const FEE_TYPE_STEP = 'step';

    // 類型
    const ALIPAY_PAY_CHANNEL_SLUG = 'ali';
    const BANK_CARD_CHANNEL_SLUG = 'bank_card';

    private static $allData = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'fee_type', 'fee', 'step_fee'];

    public function getFeeTypeTitle()
    {
        return __('paymentChannel.fee_type_list');
    }


    public function getStaticAllData() {
        if (self::$allData == null) {
            $paymentChannelCollection = $this->all();
            self::$allData = $paymentChannelCollection->all();
        }
        return self::$allData;
    }
}
