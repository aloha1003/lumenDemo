<?php

namespace App\Jobs;

use App\Services\GiftService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//送任务礼物
class SendMissionGift implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userModel;
    protected $roomModel;
    protected $giftModel;
    protected $cacheKey;
    protected $fieldKey;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userModel, $roomModel, $giftModel, $cacheKey, $fieldKey)
    {
        $this->userModel = $userModel;
        $this->roomModel = $roomModel;
        $this->giftModel = $giftModel;
        $this->cacheKey = $cacheKey;
        $this->fieldKey = $fieldKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();
            $service = app(GiftService::class);
            //
            $service->purchaseMissionGiftJob($this->userModel, $this->roomModel, $this->giftModel, $this->cacheKey, $this->fieldKey);
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
