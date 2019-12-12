<?php

namespace App\Traits;

trait GetStubTrait
{
    protected function getStub($path)
    {
        return file_get_contents(resource_path("stubs/$path.stub"));
    }
}
