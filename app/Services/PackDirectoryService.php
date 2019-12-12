<?php

namespace App\Services;

/**
 * 打包目录
 */
class PackDirectoryService
{

    public static function pack($sourcePath, $zipDestion)
    {
        $zipcreated = storage_path($zipDestion);
        self::zipData($sourcePath, $zipcreated);
        //删掉 sourcePath 所有的资料
        self::deleteDirectory($sourcePath);
        //再搭配 CLStorage
        return \CLStorage::getInstance('local')->url(encodeStoragePath('../' . $zipDestion));
    }

    /**
     * 打包zip包
     *
     * @param    [type]                   $source      [description]
     * @param    [type]                   $destination [description]
     *
     * @return   [type]                                [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T14:38:46+0800
     */
    public static function zipData($source, $destination)
    {
        if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new \ZipArchive();
                if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
                    $source = realpath($source);
                    if (is_dir($source) === true) {
                        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file) === true) {
                                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                            } else if (is_file($file) === true) {
                                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                            }
                        }
                    } else if (is_file($source) === true) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }
                return $zip->close();
            }
        }
        return false;
    }

    /**
     * 删掉目录
     *
     * @param    [type]                   $dir [description]
     *
     * @return   [type]                        [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T14:38:56+0800
     */
    public static function deleteDirectory($dir)
    {

        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}
