<?php

namespace App\Repositories;

use App\Models\SmsPasswordChangeCode;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\SmsPasswordChangeCodeRepository;

/**
 * Class SmsPasswordChangeCodeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SmsPasswordChangeCodeRepositoryEloquent extends BaseRepository implements SmsPasswordChangeCodeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SmsPasswordChangeCode::class;
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
