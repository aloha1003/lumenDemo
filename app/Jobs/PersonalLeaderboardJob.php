<?php

namespace App\Jobs;

use App\Services\LiveRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersonalLeaderboardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $anchorId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($anchorId)
    {

        $this->anchorId = $anchorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $service = app(LiveRoom::class);
            $service->updatePersonalLeaderboardByAnchorId($this->anchorId);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
