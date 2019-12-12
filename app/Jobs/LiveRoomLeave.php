<?php

namespace App\Jobs;

use App\Services\LiveRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LiveRoomLeave implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $roomId;
    public $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($roomId, $userId)
    {
        $this->roomId = $roomId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(LiveRoom::class);
        try {
            \DB::beginTransaction();
            $service->leaveCallBack($this->userId);
            \DB::commit();
            return true;
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
