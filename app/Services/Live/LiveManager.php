<?php

namespace App\Services\Live;

use App\Services\BasicFacadeInterface;
use App\Services\BasicManager;

class LiveManager extends BasicManager implements BasicFacadeInterface
{
    public function __construct($app)
    {
        app()->configure('live');
        parent::__construct($app);
        $this->default = config('live.default');
    }

    public function getInstance(string $name): array
    {
        return config("live.instances.{$name}");
    }
}
