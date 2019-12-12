<?php
namespace App\Services;

use App\Repositories\Interfaces\DestoryGoldRepository;

//金币销毀
class DestoryGoldService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(DestoryGoldRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 新增
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-10T16:03:27+0800
     */
    public function insert($data)
    {
        try {
            $newRecord = $this->repository->create($data);
            $newRecord->with('user');
            $userCurrentGold = $newRecord->user->gold_cache;
            $newRecord->user->goldUpdateSourceModel = $newRecord;
            if ($userCurrentGold <= $newRecord->gold) {
                throw new \Exception(__('destoryGold.minus_than_current_gold_exception'));
            }
            $newRecord->user->addGold(-1 * $newRecord->gold, $newRecord);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
        return $newRecord;
    }
}
