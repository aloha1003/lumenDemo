<?php

namespace App\Services;

//基本服务管理者
class BasicManager
{
    protected $app;
    protected $default;
    protected $instances;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function instance( ? string $instance = null)
    {
        $instance = $instance ?: $this->default;

        return $this->resolve($instance);
    }

    protected function resolve(string $name)
    {
        $this->instances = $this->getInstance($name);

        return new $this->instances['injection']($this->instances['config']);
    }

    /**
     * Dynamically call the default driver instance
     *
     * @param string  $method
     * @param array   $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->instance()->$method(...$parameters);
    }
}
