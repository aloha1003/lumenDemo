<?php

namespace App\Services\Sms;

use App\Services\BasicFacadeInterface;
use App\Services\BasicManager;

class SmsManager extends BasicManager implements BasicFacadeInterface
{
    public function __construct($app)
    {
        parent::__construct($app);
        app()->configure('sms');
        $this->default = config('sms.default');
    }

    public function getInstance(string $name): array
    {
        return config("sms.instances.{$name}");
    }
}
