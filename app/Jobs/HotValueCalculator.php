<?php

namespace App\Jobs;

use App\Services\LiveRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HotValueCalculator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $roomId;
    public $currentTimes;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($roomId, $currentTimes)
    {
        $this->roomId = $roomId;
        $this->currentTimes = $currentTimes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(LiveRoom::class);
        $service->doHotValueCalculator($this->roomId, $this->currentTimes);
    }
}
