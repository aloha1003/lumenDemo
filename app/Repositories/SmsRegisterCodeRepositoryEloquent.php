<?php

namespace App\Repositories;

use App\Models\SmsRegisterCode;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\SmsRegisterCodeRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SmsRegisterCodeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SmsRegisterCodeRepositoryEloquent extends BaseRepository implements SmsRegisterCodeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SmsRegisterCode::class;
    }

    /**
     * 用手機號來查找資料
     *
     * @param string $cellphone
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findByCellphone(string $cellphone): Builder
    {
        return SmsRegisterCode::where('cellphone', $cellphone);
    }

}
