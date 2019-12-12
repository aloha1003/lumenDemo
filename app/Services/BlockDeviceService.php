<?php
namespace App\Services;

use App\Repositories\Interfaces\BlockDeviceRepository;
use Carbon\Carbon;

//封设备号服务
class BlockDeviceService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(BlockDeviceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 存档
     *
     * @param    id                   $id   主键
     * @param    array                   $data 输入资料
     *
     * @return   void                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:24:25+0800
     */
    public function save($id, $data)
    {
        try {
            $record = $this->repository->find($id);
            $return = $record->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   BlockDevice               新增成功的BlockDevice
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        $data = collect($data)->only(['ip', 'user_id', 'channel', 'uuid'])->all();
        $where = [
            'user_id' => $data['user_id'],
            'channel' => $data['channel'] ?? '',
            'uuid' => $data['uuid'],
            'ip' => $data['ip'],
        ];
        $record = $this->repository->findWhere($where)->first();
        if ($record) {
            return $record;
        }
        //$data['is_block'] = $this->repository->makeModel()::IS_BLOCK_NO;
        return $this->repository->create($data);
    }
    /**
     * 删除资料
     *
     * @param    [type]                   $id [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T13:15:14+0800
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function isNeedBlockByUUID($uuid)
    {
        $result = $this->repository->findWhere([
            'uuid' => $uuid,
        ])->all();

        for ($i=0;$i<count($result);$i++) {
            if ($result[$i]->is_block) {
                return 1;
            }
        }
        return 0;
    }

    public function unblock($uuid)
    {
        $allModel = $this->repository->findWhere(['uuid' => $uuid])->all();
        $length = count($allModel);
        for ($i=0; $i<$length; $i++) {
            $model = $allModel[$i];
            $model->unblock_at = '1900-01-01 00:00:00';
            $model->save();
        }
    }

    /**
     * 依照時間區間封設備號
     */
    public function blockWithDayRange($uuid, $dayRange)
    {
        $now = Carbon::now();
        $unblockDate = $now;
        switch($dayRange) {
            case 3:
                $unblockDate->addDays(3);
            break;
            case 7:
                $unblockDate->addDays(7);
            break;
            case 30:
                $unblockDate->addDays(30);
            break;
            case 'all':
                $unblockDate = '2500-01-01 00:00:00';
            break;
        }
        $allModel = $this->repository->findWhere(['uuid' => $uuid])->all();
        $length = count($allModel);
        $idList = [];
        for ($i=0; $i<$length; $i++) {
            $model = $allModel[$i];
            $model->unblock_at = $unblockDate;
            $model->save();
            $idList[] = $model->user_id;
        }
        return $idList;
    }

    /**
     * 用IM傳送封設備號通知到app
     */
    public function sendBlockMessageWithIM($idList)
    {
        $length = count($idList);
        for ($i=0; $i<$length; $i++) {
            $broadCastData = [
                'GroupId' => 'Common',
                'MsgBody' => [
                    [
                        'MsgType' => 'TIMCustomElem',
                    ],
                ],
            ];
            $result = \IM::sendBroadCast($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.104', ['userId' => $idList[$i]])]]);
        }
    }
}
