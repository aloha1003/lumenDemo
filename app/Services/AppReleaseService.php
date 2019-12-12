<?php

namespace App\Services;

use App\Exceptions\ErrorCode;
use App\Repositories\Interfaces\AppReleaseRepository;
use App\Repositories\Interfaces\ChannelRepository;

//版本更新服务
class AppReleaseService
{
    private $channelRepository;
    private $appReleaseRepository;

    const APP_RELEASE_KEY = 'release:app_data';

    public function __construct(
        ChannelRepository $channelRepository,
        AppReleaseRepository $appReleaseRepository) {
        $this->channelRepository = $channelRepository;
        $this->appReleaseRepository = $appReleaseRepository;
    }

    public function writeAppReleaseDataToCache($channelSlug, $data)
    {
        $key = self::APP_RELEASE_KEY . ':' . $channelSlug;

        \Cache::forever($key, $data);
    }

    /**
     * 從cache取得app已發布的資料
     */
    public function getAppReleaseDataFromCache($channelSlug)
    {
        $key = self::APP_RELEASE_KEY . ':' . $channelSlug;

        return \Cache::get($key);
    }

    /**
     * 從db取得app已發布的資料
     */
    public function getAppReleaseDataFromDB($channelSlug)
    {
        $channelModel = $this->channelRepository->findWhere(['key_code' => $channelSlug])->first();
        if ($channelModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }
        $appRelease = $this->appReleaseRepository->makeModel();
        $iosReleaseModel = $this->appReleaseRepository->findWhere(
            [
                'channel_key_code' => $channelSlug,
                'ios_on' => $appRelease::ON_RELEASE,
            ]
        )->first();
        $androidReleaseModel = $this->appReleaseRepository->findWhere(
            [
                'channel_key_code' => $channelSlug,
                'android_on' => $appRelease::ON_RELEASE,
            ]
        )->first();

        $result['official_url'] = $channelModel->official_url;

        $result['ios'] = [];
        $result['android'] = [];
        if ($androidReleaseModel != null) {
            $data = [
                'android_version_number' => $androidReleaseModel->android_version_number,
                'android_version_code' => $androidReleaseModel->android_version_code,
                // 'download_url' => $androidReleaseModel->android_remote_download_url,
                'download_url' => ($androidReleaseModel->android_cdn_url) ? $androidReleaseModel->android_cdn_url : cdnUrl($androidReleaseModel->android_remote_download_url),
            ];
            $result['android'] = $data;
        }
        if ($iosReleaseModel != null) {
            $data = [
                'ios_version_number' => $iosReleaseModel->ios_version_number,
                'ios_version_code' => $iosReleaseModel->ios_version_code,
                // 'download_url' => $iosReleaseModel->ios_remote_download_url,
                'download_url' => $iosReleaseModel->ios_cdn_url ? $iosReleaseModel->ios_cdn_url : cdnUrl($iosReleaseModel->ios_remote_download_url),
            ];
            $result['ios'] = $data;
        }
        return $result;
    }

    /**
     * 取得渠道已發佈的資料
     */
    public function getAllRelease($channelSlug)
    {
        $appData = $this->getAppReleaseDataFromCache($channelSlug);
        if ($appData == null && $appData == []) {
            $appData = $this->getAppReleaseDataFromDB($channelSlug);
            $this->writeAppReleaseDataToCache($channelSlug, $appData);
        }
        return $appData;
    }

    /**
     * 發布ios
     */
    public function releaseIOS($id)
    {
        $releaseModel = $this->appReleaseRepository->findWhere(['id' => $id])->first();
        if ($releaseModel == null) {
            return;
        }

        $keyCode = $releaseModel->channel_key_code;

        $modelArray = $this->appReleaseRepository->findWhere(['channel_key_code' => $keyCode])->all();
        $length = count($modelArray);
        for ($i = 0; $i < $length; $i++) {
            $model = $modelArray[$i];
            if ($model->id == $releaseModel->id) {
                continue;
            }
            if ($model->ios_on == 1) {
                $model->ios_on = 0;
                $model->save();
            }
        }
        // $releaseModel->ios_cdn_url = $data['ios_cdn_url'] ?? $releaseModel->ios_cdn_url;
        $releaseModel->ios_cdn_url = cdnUrl($releaseModel->ios_remote_download_url);
        $releaseModel->ios_on = 1;
        $releaseModel->save();
        \Queue::pushOn(pool('default'), new \App\Jobs\CDNPrefetch($releaseModel->android_cdn_url));

    }

    /**
     * 發布android
     */
    public function releaseAndroid($id)
    {
        $releaseModel = $this->appReleaseRepository->findWhere(['id' => $id])->first();
        if ($releaseModel == null) {
            return;
        }

        $keyCode = $releaseModel->channel_key_code;

        $modelArray = $this->appReleaseRepository->findWhere(['channel_key_code' => $keyCode])->all();
        $length = count($modelArray);
        for ($i = 0; $i < $length; $i++) {
            $model = $modelArray[$i];
            if ($model->id == $releaseModel->id) {
                continue;
            }
            if ($model->android_on == 1) {
                $model->android_on = 0;
                $model->save();
            }
        }
        $releaseModel->android_cdn_url = cdnUrl($releaseModel->android_remote_download_url);
        $releaseModel->android_on = 1;
        $releaseModel->save();
        \Queue::pushOn(pool('default'), new \App\Jobs\CDNPrefetch($releaseModel->android_cdn_url));
    }

    /**
     * 新增一筆渠道資料
     *
     * @param array $parameters
     */
    public function create($parameters)
    {
        $now = date("Y-m-d H:i:s", time());

        $keyCode = $parameters['channel_key_code'];
        $channelModel = $this->channelRepository->findWhere(
            ['key_code' => $parameters['channel_key_code']]
        )->first();

        if ($channelModel == null) {
            throw new \Exception(__('appRelease.key_code_error'));
        }

        $androidUrl = \Storage::disk('local')->putFileAs('android/' . config('app.app_release_prefix') . $parameters['android_version_code'] . '_' . $parameters['android_version_number'], $parameters['android_file'], config('app.app_release_prefix') . $parameters['channel_key_code'] . '_' . $parameters['android_version_code'] . '_' . $parameters['android_version_number'] . '.apk');

        $iosUrl = \Storage::disk('local')->putFileAs('ios/' . config('app.app_release_prefix') . $parameters['ios_version_code'] . '_' . $parameters['ios_version_number'], $parameters['ios_file'], config('app.app_release_prefix') . $parameters['channel_key_code'] . '_' . $parameters['ios_version_code'] . '_' . $parameters['ios_version_number'] . '.ipa');

        // 準備寫入資料表的資料
        $data = [
            'channel_key_code' => $parameters['channel_key_code'],
            'ios_local_download_url' => $iosUrl,
            'ios_version_code' => $parameters['ios_version_code'],
            'ios_version_number' => $parameters['ios_version_number'],

            'android_local_download_url' => $androidUrl,
            'android_version_code' => $parameters['android_version_code'],
            'android_version_number' => $parameters['android_version_number'],

            'created_at' => $now,
            'updated_at' => $now,
        ];
        // 寫入資料表
        $model = $this->appReleaseRepository->create($data);
        $this->syncAndroid($model);
        $this->syncIos($model);
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
     * 取得所有的最新版本的cdn路径
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T15:41:49+0800
     */
    public function obtainAllLatest()
    {

        $releaseModel = $this->appReleaseRepository->all(['']);
    }

    /**
     * 同步安卓
     *
     * @param    [type]                   $releaseModel [description]
     *
     * @return   [type]                                 [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T16:29:29+0800
     */
    private function syncAndroid($releaseModel)
    {
        // 將檔案同步到官方網站
        if (env('APP_RELEASE_HOST_USER') != '' && env('APP_RELEASE_HOST') != '' && env('APP_ANDROID_RELEASE_DIR') != '' && $releaseModel->android_local_download_url) {
            $localPath = storage_path('app/' . $releaseModel->android_local_download_url);

            $targetHostUser = env('APP_RELEASE_HOST_USER');
            $targetHost = env('APP_RELEASE_HOST');
            $targetFilePath = env('APP_ANDROID_RELEASE_DIR');
            $targetFilePath .= $releaseModel->channel_key_code . '/';
            $targetFilePath .= $releaseModel->android_local_download_url;
            $remoteDir = dirname($targetFilePath);
            $command = "rsync -a --rsync-path=\"mkdir -p {$remoteDir} && rsync\"   $localPath {$targetHostUser}@{$targetHost}:{$targetFilePath}";
            shell_exec($command);
        }
    }

    /**
     * 同步IOS
     *
     * @param    [type]                   $releaseModel [description]
     *
     * @return   [type]                                 [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-28T16:31:55+0800
     */
    private function syncIos($releaseModel)
    {
        // 將檔案同步到官方網站
        if (env('APP_RELEASE_HOST_USER') != '' && env('APP_RELEASE_HOST') != '' && env('APP_IOS_RELEASE_FILE') != '' && $releaseModel->ios_local_download_url) {
            $localPath = storage_path('app/' . $releaseModel->ios_local_download_url);
            $targetHostUser = env('APP_RELEASE_HOST_USER');
            $targetHost = env('APP_RELEASE_HOST');
            $targetFilePath = env('APP_IOS_RELEASE_DIR');
            $targetFilePath .= $releaseModel->channel_key_code . '/';
            $targetFilePath .= $releaseModel->ios_local_download_url;
            $remoteDir = dirname($targetFilePath);
            $command = "rsync -a --rsync-path=\"mkdir -p {$remoteDir} && rsync\"   $localPath {$targetHostUser}@{$targetHost}:{$targetFilePath}";
            shell_exec($command);
        }
    }

    /**
     * 取得所有 已发布的CDN连结
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-12-02T09:36:20+0800
     */
    public function allRelease()
    {

        $appRelease = $this->appReleaseRepository->makeModel();
        $iosReleaseList = $this->appReleaseRepository->findWhere(
            [
                'ios_on' => $appRelease::ON_RELEASE,
            ],
            ['ios_local_download_url', 'channel_key_code', \DB::raw('"ios" as platform')]
        )->map(function ($app) {
            $app->cdn_url = cdnUrl($app->ios_remote_download_url);
            return $app;
        })
            ->toArray()
        ;
        $androidReleaseList = $this->appReleaseRepository->findWhere(
            [
                'android_on' => $appRelease::ON_RELEASE,
            ],
            ['android_local_download_url', 'channel_key_code', \DB::raw('"android" as platform')]
        )->map(function ($app) {
            $app->cdn_url = cdnUrl($app->android_remote_download_url);
            return $app;
        })
            ->toArray()
        ;
        $result = array_merge($iosReleaseList, $androidReleaseList);
        return $result;
    }
}
