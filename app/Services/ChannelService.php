<?php

namespace App\Services;

use App\Repositories\Interfaces\ChannelRepository;
use App\Traits\GetStubTrait;

/**
 * 渠道設定與資料讀取
 */
class ChannelService
{
    use GetStubTrait;
    private $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * 新增一筆渠道資料
     *
     * @param array $parameters
     */
    public function create($parameters)
    {
        $now = date("Y-m-d H:i:s", time());

        // 準備寫入資料表的資料
        $data = [
            'name' => $parameters['name'],
            'key_code' => $parameters['key_code'],
            'created_at' => $now,
            'updated_at' => $now,
        ];
        if (isset($parameters['official_url'])) {
            $data['official_url'] = $parameters['official_url'];
        }

        if (isset($parameters['ios_official_download_url'])) {
            $data['ios_official_download_url'] = $parameters['ios_official_download_url'];
        }
        if (isset($parameters['ios_official_download_cdn_url'])) {
            $data['ios_official_download_cdn_url'] = $parameters['ios_official_download_cdn_url'];
        }
        if (isset($parameters['android_official_download_url'])) {
            $data['android_official_download_url'] = $parameters['android_official_download_url'];
        }
        if (isset($parameters['android_official_download_cdn_url'])) {
            $data['android_official_download_cdn_url'] = $parameters['android_official_download_cdn_url'];
        }

        if (isset($parameters['comment'])) {
            $data['comment'] = $parameters['comment'];
        }

        // 寫入資料表
        $this->channelRepository->create($data);
    }

    /**
     * 更新一筆渠道資料
     */
    public function update($parameters)
    {
        $record = $this->channelRepository->findWhere(['id' => $parameters['channel_id']]);
        if ($record->count() == 0) {
            throw new \Exception('invalid channel id');
        }

        $record = $record->first();

        $record->name = isset($parameters['name']) ? $parameters['name'] : $record->name;
        $record->key_code = isset($parameters['key_code']) ? $parameters['key_code'] : $record->key_code;

        if (isset($parameters['official_url'])) {
            $record->official_url = $parameters['official_url'];
        }

        if (isset($parameters['ios_official_download_url'])) {
            $record->ios_official_download_url = $parameters['ios_official_download_url'];
        }
        if (isset($parameters['ios_official_download_cdn_url'])) {
            $record->ios_official_download_cdn_url = $parameters['ios_official_download_cdn_url'];
        }

        if (isset($parameters['android_official_download_url'])) {
            $record->android_official_download_url = $parameters['android_official_download_url'];
        }
        if (isset($parameters['android_official_download_cdn_url'])) {
            $record->android_official_download_cdn_url = $parameters['android_official_download_cdn_url'];
        }

        $record->comment = isset($parameters['comment']) ? $parameters['comment'] : $record->comment;
        $record->save();
    }

    /**
     * 用key code來取得channel的model
     */
    public function getChannelModelByKey($key)
    {
        $collection = $this->channelRepository->findWhere(['key_code' => $key]);

        return $collection->first();
    }

    /**
     * 檢查渠道key是否合法
     */
    public function checkKeyIsValid($key)
    {
        if ($this->getChannelModelByKey($key) == null) {
            return false;
        }
        return true;
    }

    /**
     * 执行打包作业
     *
     * @param    [type]                   $channelId [description]
     *
     * @return   [type]                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T13:04:03+0800
     */
    public function doPackJob($channelKey)
    {
        //先从 db 取设定回来
        $channel = $this->getChannelModelByKey($channelKey);
        //先产生对应的目录
        $targetDir = 'build/' . $channelKey;
        $targetDirPath = storage_path($targetDir);
        $this->copyDir(resource_path('stubs/ChannelDeploy'), $targetDir);
        //更新落地页里面的名称
        $indexHtmlTemplate = str_replace(
            ['{{appName}}'],
            [config('app.display_name')],
            $this->getStub('ChannelDeploy/index')
        );
        $indexHtmlPath = $targetDirPath . '/index.php';
        file_put_contents($indexHtmlPath, $indexHtmlTemplate);
        //把先前复制的index.stub删了，免得让人嘴
        unlink($targetDirPath . '/index.stub');
        //更新下载连结
        $mainJsTemplate = str_replace(
            ['{{official_url}}'],
            [$channel->official_url],
            $this->getStub('ChannelDeploy/assets/js/main')
        );
        $mainJsPath = $targetDirPath . '/assets/js/main.js';
        file_put_contents($mainJsPath, $mainJsTemplate);
        //把先前复制的stub删了，免得让人嘴
        unlink($targetDirPath . '/assets/js/main.stub');
        //打包目录所有的档案
        $zipName = app(PackDirectoryService::class)->pack($targetDirPath, "build/{$channelKey}-v1.zip");
        return $zipName;
    }
    /**
     * 复制目录所有档案
     *
     * @param    [type]                   $src [description]
     * @param    [type]                   $dst [description]
     *
     * @return   [type]                        [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T13:25:15+0800
     */
    public function copyDir($src, $dst)
    {
        $dstStorPath = storage_path($dst);
        $dir = opendir($src);
        @mkdir($dstStorPath, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dstStorPath . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
