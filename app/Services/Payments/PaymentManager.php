<?php
namespace App\Services\Payments;

class PaymentManager
{
    protected $class;
    protected $instances = [];
    public function __construct()
    {

    }
    public function getInstance($channel, $paymentType)
    {
        $instanceName = 'App\\Services\\Payments\\Instances\\' . ucfirst($channel) . '\\' . ucfirst($paymentType) . 'Payment';
        if (!class_exists($instanceName)) {
            throw new \Exception(__('payment.has_not_define', ['class' => $instanceName]));
        }
        if (!isset($this->instances[$instanceName])) {
            $this->instances[$instanceName] = app($instanceName);
        }
        return $this->instances[$instanceName];
    }

    public function instance( ? string $instance = null)
    {
        $instance = $instance ?: $this->class;
        $this->instance = $this->getInstance($instance);
        return $this->instance;
    }
}
