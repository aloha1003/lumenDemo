<?php

namespace App\Jobs;

use App\Services\GameService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GameBet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $betRecordId;
    protected $betGold;
    protected $userId;
    protected $cacheKey;
    protected $fieldKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($betRecordId, $betGold, $userId, $cacheKey, $fieldKey)
    {

        $this->betRecordId = $betRecordId;
        $this->betGold = $betGold;
        $this->userId = $userId;
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
            $service = app(GameService::class);
            $service->betJob($this->betRecordId, $this->betGold, $this->userId, $this->cacheKey, $this->fieldKey);
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
