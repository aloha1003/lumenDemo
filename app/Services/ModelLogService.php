<?php
namespace App\Services;

use App\Repositories\Interfaces\ModelLogRepository;

//表格Log服务
class ModelLogService
{
    private $repo;
    public function __construct(ModelLogRepository $repo)
    {
        $this->repo = $repo;
    }

    public function writeLog($model, $currentEnv, $request, $platformId, $ip, $original = [])
    {
        $modelLog = $this->repo->makeModel();
        $diff = $this->modelDiff($model, $original);
        $data = [
            'model_name' => get_class($model),
            'model_primary_key_column' => $model->getKeyName(),
            'model_id' => $model->getKey(),
            'user_id' => $platformId,
            'ip' => $ip,
            'origin_data' => json_encode($model->getOriginal()),
            'diff_data' => json_encode($diff),
            'env' => $request,
            'update_source' => $currentEnv,
        ];
        $modelLog->create($data);
    }

    /**
     * 取得差異的部份
     *
     * @param    Model                   $now    [description]
     *
     * @return   array                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T09:26:49+0800
     */
    private function modelDiff($model, $origin = [])
    {
        $origin = ($origin) ?? $model->getOriginal();

        $now = $model->toArray();

        if ($origin) {
            foreach ($now as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($now[$key]);
                }
            }
            foreach ($origin as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    unset($origin[$key]);
                }
            }
            $diff = array_diff($now, $origin);
        } else {
            $diff = $now;
        }
        return $diff;
    }
}
