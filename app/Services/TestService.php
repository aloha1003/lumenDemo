<?php

namespace App\Services;

class TestService
{
    private $repository;

    public function __construct()
    {

    }

    public function lists(array $data)
    {
        return collect([
            ['id' => 1, 'name' => 'aaa', 'remark' => 'hello word'],
            ['id' => 2, 'name' => 'bbb', 'remark' => 'hello word'],
        ]);
    }
}
