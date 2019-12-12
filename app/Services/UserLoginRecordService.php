<?php
namespace App\Services;

use App\Repositories\Interfaces\UserLoginRecordRepository;
use Carbon\Carbon;

//用户登入记录
class UserLoginRecordService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(UserLoginRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 更新用户登入记录
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-25T17:46:11+0800
     */
    public function insert($data)
    {
        $data = collect($data)->only(['user_id', 'device_type', 'ip'])->all();
        $record = $this->repository->findWhere([['created_at', '>=', Carbon::now()->addMinutes(-10)], 'user_id' => $data['user_id']])->first();
        if ($record) {
            $record->update($data);
        } else {
            return $this->repository->create($data);
        }
        return $record;
    }
}
