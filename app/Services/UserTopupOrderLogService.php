<?php
namespace App\Services;

use App\Repositories\Interfaces\UserTopupOrderLogRepository;

//表格Log服务
class UserTopupOrderLogService
{
    private $repo;
    public function __construct(UserTopupOrderLogRepository $repo)
    {
        $this->repo = $repo;
    }

    public function writeLog($model, $currentEnv, $request, $platformId, $ip, $original = [], $diff)
    {
        $modelLog = $this->repo->makeModel();
        $data = [
            'transaction_no' => $model->transaction_no,
            'pay_step' => $model->pay_step,
            'user_id' => $platformId,
            'ip' => $ip,
            'origin_data' => json_encode($original),
            'diff_data' => $diff,
            'env' => $request,
            'payload' => json_encode($model->payload),
        ];
        $modelLog->create($data);
    }
}
