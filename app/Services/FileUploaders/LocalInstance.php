<?php

namespace App\Services\FileUploaders;

class LocalInstance implements FileCommonFunctionsInterface
{
    public function upload(string $photoPath, $source = null, $rename = "")
    {
        if (is_string($source)) {
            $photoPath .= '/' . basename($source);
            $source = file_get_contents($source);
        }
        if ($rename) {
            $path = \Storage::putFileAs($photoPath, $source, $rename);
        } else {
            $path = \Storage::put($photoPath, $source);
        }
        if (is_bool($path)) {
            $path = $photoPath;
        }
        return encodeStoragePath($path);
    }

    public function delete($path)
    {
        return \Storage::delete($path);
    }

    public function url($path)
    {
        return url($path);
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
        return \Storage::$method(...$parameters);
    }
}
