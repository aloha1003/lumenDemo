<?php

namespace App\Jobs;

use App\Services\BarrageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBarrage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userModel;
    protected $roomModel;
    protected $barrageModel;
    protected $message;

    protected $cacheKey;
    protected $fieldKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userModel, $barrageModel, $roomModel, $message, $cacheKey, $fieldKey)
    {

        $this->userModel = $userModel;
        $this->roomModel = $roomModel;
        $this->barrageModel = $barrageModel;
        $this->message = $message;

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
            $service = app(BarrageService::class);
            $service->purchaseJob($this->userModel, $this->barrageModel, $this->roomModel, $this->message, $this->cacheKey, $this->fieldKey);
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
