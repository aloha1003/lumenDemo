<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;
use App\Services\AppReleaseService;

class ReleaseAPIController extends Controller
{
    private $service;
    public function __construct(AppReleaseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $list = $this->service->allRelease();
        return response()->success($list, 200);
    }
}
