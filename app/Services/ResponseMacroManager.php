<?php

namespace App\Services;

// use Illuminate\Contracts\Routing\ResponseFactory;
use Laravel\Lumen\Http\ResponseFactory;

//回应巨集管理者
class ResponseMacroManager
{
    protected $macros = [];

    public function __construct(ResponseFactory $factory)
    {
        $this->macros = [
            Responses\Macro\Success::class,
            Responses\Macro\Error::class,
            Responses\Macro\Download::class,
        ];
        $this->bindMacros($factory);
    }

    public function bindMacros($factory)
    {
        foreach ($this->macros as $macro) {
            (new $macro)->run($factory);
        }
    }
}
