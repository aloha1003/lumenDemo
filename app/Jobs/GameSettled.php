<?php

namespace App\Jobs;

use App\Services\GameService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GameSettled implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $status;
    protected $betRecordId;
    protected $userId;
    protected $winGold;

    protected $cacheKey;
    protected $fieldKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($status, $betRecordId, $userId, $winGold, $cacheKey, $fieldKey)
    {
        $this->status = $status;
        $this->betRecordId = $betRecordId;
        $this->userId = $userId;
        $this->winGold = $winGold;

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
            $service->betSettledJob($this->status, $this->betRecordId, $this->userId, $this->winGold, $this->cacheKey, $this->fieldKey);
            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
