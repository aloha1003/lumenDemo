<?php

namespace App\Jobs;

use App\Services\UserTopupOrderLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 插入充值Log
// 在更新/删除/新增 资料的时候会触发
class UserTopupOrderLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * DB Model
     *
     * @var App\Models\UserTopupOrder
     */
    public $model;

    /**
     * 操作当下的请求资料
     *
     * @var json string
     */
    public $request;
    /**
     * 操作当下的用户ID
     *
     * @var int
     */
    public $platformId;
    /**
     * 操作当下的 IP
     *
     * @var string
     */
    public $ip;
    /**
     * 操作当下的原始资料
     *
     * @var array
     */
    public $original;
    /**
     * 操作当下差异
     *
     * @var array
     */
    public $diff;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $request, $platformId, $ip, $original = [], $diff)
    {
        $this->model = $model;
        $this->request = $request;
        $this->platformId = $platformId;
        $this->ip = $ip;
        $this->original = $original;
        $this->diff = $diff;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(UserTopupOrderLogService::class);
        try {
            \DB::beginTransaction();
            $service->writeLog($this->model, $this->currentEnv, $this->request, $this->platformId, $this->ip, $this->original);
            \DB::commit();
            return true;
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }
}
