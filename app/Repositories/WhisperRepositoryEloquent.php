<?php

namespace App\Repositories;

use App\Models\Whisper;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\whisperRepository;

/**
 * Class WhisperRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WhisperRepositoryEloquent extends BaseRepository implements WhisperRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Whisper::class;
    }

}
