<?php

namespace App\Services\FileUploaders;

interface FileCommonFunctionsInterface
{
    /**
     * 上傳方法
     *
     * @param    string                   $photoPath 存放的目標目錄
     * @param    object                   $source    考慮其他地方可能不會有file上傳，所以允許留空
     * @param    string                   $rename    更名
     *
     * @return   [type]                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-29T09:14:15+0800
     */
    public function upload(string $photoPath, $source = null, $rename = '');
    /**
     * 刪除
     *
     * @param    string/array                   $path
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-29T09:20:00+0800
     */
    public function delete($path);

    /**
     * 下載
     *
     * @param    [type]                   $path [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-29T10:49:51+0800
     */
    public function url($path);
}
