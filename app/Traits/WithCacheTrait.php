<?php

namespace App\Traits;

trait WithCacheTrait
{
    public function withCache($attribute)
    {
        $cacheEnabled = config('repository.cache.enabled');
        if ($cacheEnabled) {
            $className = getRepositoryByName(underLineToCamelCase($attribute));
            $relationShipIdColumn = $this->relationShipIdMap[$attribute][0] ?? '';
            if (!$relationShipIdColumn) {
                throw new \Exception("Must Defined relationShip " . $attribute . " First");
            }
            $cacheKey = repositoryCacheKey($className, 'findWhere', [$relationShipIdColumn => $this->id]);
            $that = $this;
            return \Cache::remember($cacheKey, config('repository.cache.minutes'), function () use ($attribute, $that) {
                return $that->$attribute()->getResults();
            });
        } else {
            return $this->$attribute()->getResults();
        }
    }
}
