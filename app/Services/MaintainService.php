<?php

namespace App\Services;

use App\Models\Maintain as MaintainModel;
use App\Repositories\Interfaces\MaintainRepository;

//维护服务
class MaintainService
{
    private $maintainRepository;

    public function __construct(MaintainRepository $maintainRepository)
    {
        $this->maintainRepository = $maintainRepository;
    }

    public function save($data)
    {
        if (isset($data['start_datetime']) == false || $data['start_datetime'] == null) {
            $data['start_datetime'] = null;
        }
        if (isset($data['end_datetime']) == false || $data['end_datetime'] == null) {
            $data['end_datetime'] = null;
        }
        if (isset($data['front_comment']) == false || $data['front_comment'] == null) {
            $data['front_comment'] = '';
        }

        $this->maintainRepository->update(
            $data,
            MaintainModel::MAINTAIN_ID
        );
    }
}
