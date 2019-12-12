<?php

namespace App\Jobs;

use App\Services\ModelLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 插入操作纪录
// 在更新/删除/新增 资料的时候会触发
class ModelLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * DB Model
     *
     * @var App\Models\BaseModel
     */
    public $model;
    /**
     * 操作当下的平台
     *
     * @var string
     */
    public $currentEnv;
    /**
     * 操作当下的请求资料
     *
     * @var [type]
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
     * @var [type]
     */
    public $original;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $currentEnv, $request, $platformId, $ip, $original = [])
    {
        $this->model = $model;
        $this->currentEnv = $currentEnv;
        $this->request = $request;
        $this->platformId = $platformId;
        $this->ip = $ip;
        $this->original = $original;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = app(ModelLogService::class);
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
