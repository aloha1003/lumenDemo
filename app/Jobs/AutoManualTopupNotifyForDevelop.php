<?php

namespace App\Jobs;

use App\Services\UserTopupOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//在开发模式中自动测试手动充值
class AutoManualTopupNotifyForDevelop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $transactionNo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionNo)
    {
        $this->transactionNo = $transactionNo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('app.env') != 'production') {
            //只有在非正式环境才能启用
            try {
                \DB::beginTransaction();
                $service = app(UserTopupOrderService::class);
                $service->manualSendNotify($this->transactionNo);
                \DB::commit();
                echo '手动充值测试完成,订单编号:' . $this->transactionNo;
            } catch (\Exception $ex) {
                \DB::rollback();
                wl($ex);
            }
        }
    }
}
