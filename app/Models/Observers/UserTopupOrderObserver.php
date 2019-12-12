<?php
namespace App\Models\Observers;

use App\Models\UserTopupOrderLog;

class UserTopupOrderObserver
{
    protected $modelDirtyData = [];
    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function saved($model)
    {
        $origin = $model->getOriginal();
        $this->writeLog($model);
        switch ($model->pay_step) {
            case $model::PAY_STEP_SUCCESS:
                if ($origin['pay_step'] == $model::PAY_STEP_SUCCESS) {
                    //如果状态没有变动的话，就跳过
                    break;
                }
                $model->with('user');
                $userCurrentGold = $model->user->gold;
                $model->user->addGold($model->gold, $model);
                break;
            default:
                //Do nothing
                break;
        }

    }

    public function saving($model)
    {
        $origin = $model->getOriginal();
        //根据不同的状态，做不同的事
        switch ($model->pay_step) {
            case $model::PAY_STEP_SUCCESS:
                if ($origin['pay_step'] == $model::PAY_STEP_SUCCESS) {
                    //如果状态没有变动的话，就跳过
                    break;
                }
                //TODO transaction to service
                //支付成功，上分之前要做上一个状态是不是 PEND 或是 第三方回传错误 以及 pay_at是否为空
                if (!($origin['pay_step'] == $model::PAY_STEP_PEND || $origin['pay_step'] == $model::PAY_STEP_THIRD_CALLBACK_ERR)) {
                    throw new \Exception(__('userTopupOrder.error_handler_illegal_pay_step'));
                }
                if ($origin['pay_at']) {
                    throw new \Exception(__('userTopupOrder.has_finish_this_order'));
                }
                $model->gold = coinToGold($model->amount);
                break;
            default:
                //Do nothing
                break;
        }
    }

    private function writeLog($model)
    {
        //TODO job
        $modelLog = app(UserTopupOrderLog::class);

        $diff = $this->modelDiff($model);
        $data = [
            'transaction_no' => $model->transaction_no,
            'pay_step' => $model->pay_step,
            'user_id' => platformId(),
            'ip' => ip(),
            'origin_data' => json_encode($model->getOriginal()),
            'diff_data' => json_encode($diff),
            'env' => json_encode(request()->all()),
            'payload' => json_encode($model->payload),
        ];
        $modelLog->create($data);
    }

    private function modelDiff($model)
    {
        $origin = $model->getOriginal();

        $now = $model->toArray();

        if ($origin) {
            foreach ($now as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($now[$key]);
                }
            }
            foreach ($origin as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($origin[$key]);
                }
            }
            $diff = array_diff($now, $origin);
        } else {
            $diff = $now;
        }
        return $diff;
    }

}
