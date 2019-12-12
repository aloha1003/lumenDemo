<?php

namespace App\Traits;

trait CacheableRefractor
{
    /**
     * Get Cache key for the method
     *
     * @param $method
     * @param $args
     *
     * @return string
     */
    public function getCacheKey($method, $args = null)
    {
        $request = app('Illuminate\Http\Request');
        $args = serialize($args);
        $criteria = $this->serializeCriteria();
        $targetCriteria = $this->getCriteria()->toArray();
        $requestData = $request->all();
        if (method_exists($this, 'getInjectUrlData')) {
            $requestData = array_merge($requestData, $this->getInjectUrlData());
        }
        $calledClass = get_called_class();
        $calledClass = str_replace("\\","_",$calledClass);

        $key = sprintf('l5_auto_cache:%s:%s:%s', $calledClass, $method, md5($args . $criteria . json_encode($requestData)));
        return $key;
    }
}
