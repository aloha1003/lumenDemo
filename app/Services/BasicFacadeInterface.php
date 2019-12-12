<?php

namespace App\Services;

//基本服务Facade
interface BasicFacadeInterface
{
    function getInstance(string $name): array;
}
