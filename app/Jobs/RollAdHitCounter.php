<?php

namespace App\Jobs;

use App\Services\RollAdService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RollAdHitCounter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $rollAdId;
    public $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $rollAdId)
    {
        $this->userId = $userId;
        $this->rollAdId = $rollAdId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(RollAdService::class);
        try {
            \DB::beginTransaction();
            $service->hitCounter($this->userId, $this->rollAdId);
            \DB::commit();
            return true;
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
