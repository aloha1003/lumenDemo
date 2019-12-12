<?php

namespace App\Repositories;

use App\Models\Manager;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ManagerRepository;

/**
 * Class ManagerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ManagerRepositoryEloquent extends BaseRepository implements ManagerRepository
{
    protected $fieldSearchable = [
        'id',
        'parent_id',
        'name',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Manager::class;
    }

    public function updateGold($managerModel, $gold, $sourceModel)
    {
        $managerModel->goldUpdateSourceModel = $sourceModel;
        $managerModel->gold = $gold;
        $managerModel->save();
    }

}
