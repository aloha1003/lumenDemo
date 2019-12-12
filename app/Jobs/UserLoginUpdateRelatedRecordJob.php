<?php

namespace App\Jobs;

use App\Services\UserLoginService;
use App\Traits\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//前台用户成功登入的时候触发
class UserLoginUpdateRelatedRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    public $parameters;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $parameters)
    {
        $this->user = $user;
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(UserLoginService::class);
        try {
            \DB::beginTransaction();
            $service->updateRelatedRecordJob($this->user, $this->parameters);
            \DB::commit();
            return true;
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
