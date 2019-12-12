<?php
namespace App\Services\FileUploaders;

class FileUploadManager
{
    protected $class = '';
    protected $instances = [];
    public function __construct()
    {
        $this->class = config('filesystems.default');
        // $this->class = 'App\\Services\\FileUploaders\\' . ucfirst($this->default) . 'Instance';
    }

    /**
     * 返回 instanace class
     *
     * @param    [type]                   $instance [description]
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-30T10:38:49+0800
     */
    private function getInstanceName($instance)
    {
        return 'App\\Services\\FileUploaders\\' . ucfirst($instance) . 'Instance';
    }

    public function getInstance(string $instanceName)
    {
        if (!isset($this->instances[$instanceName])) {
            $this->instances[$instanceName] = app($this->getInstanceName($instanceName));
        }
        return $this->instances[$instanceName];
    }

    public function instance( ? string $instance = null)
    {
        $instance = $instance ?: $this->class;
        $this->instance = $this->getInstance($instance);
        return $this->instance;
    }

    public function resetInstance(string $instanceName = '')
    {
        $instance = $instanceName ?: $this->class;
        unset($this->instances[$instance]);
        return $this->instance($instanceName);
    }
    /*
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
