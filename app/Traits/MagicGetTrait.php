<?php

namespace App\Traits;


trait MagicGetTrait
{
	public function __call($name, $arguments)
	{
		if (startsWith($name, 'get')) {
			$property = lcfirst(substr($name, 3));
			if (property_exists($this, $property)) {
				return $this->$property;
			} else {
				throw new \Exception("$property is not accessible.");
			}
		}
		$property = $name;
		if (property_exists($this, $property)) {
			return $this->$property;
		} else {
			throw new \Exception("$property is not accessible.");
		}
		return null;
	}
}