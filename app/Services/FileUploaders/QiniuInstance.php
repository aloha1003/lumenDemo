<?php

namespace App\Services\FileUploaders;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use zgldh\QiniuStorage\Plugins\AvInfo;
use zgldh\QiniuStorage\Plugins\DownloadUrl;
use zgldh\QiniuStorage\Plugins\Fetch;
use zgldh\QiniuStorage\Plugins\ImageExif;
use zgldh\QiniuStorage\Plugins\ImageInfo;
use zgldh\QiniuStorage\Plugins\ImagePreviewUrl;
use zgldh\QiniuStorage\Plugins\LastReturn;
use zgldh\QiniuStorage\Plugins\PersistentFop;
use zgldh\QiniuStorage\Plugins\PersistentStatus;
use zgldh\QiniuStorage\Plugins\PrivateDownloadUrl;
use zgldh\QiniuStorage\Plugins\PrivateImagePreviewUrl;
use zgldh\QiniuStorage\Plugins\Qetag;
use zgldh\QiniuStorage\Plugins\UploadToken;
use zgldh\QiniuStorage\Plugins\VerifyCallback;
use zgldh\QiniuStorage\Plugins\WithUploadToken;
use zgldh\QiniuStorage\QiniuAdapter;
use zgldh\QiniuStorage\QiniuStorage;

class QiniuInstance implements FileCommonFunctionsInterface
{
    public function __construct()
    {
        $this->initConfigFromCache();
    }
    protected function initConfigFromCache()
    {
        $this->boot();
        $sc = sc('qiniu');
        config(
            [
                'disks.qiniu.domains.default' => $sc['QINIU_DOMAIN'],
                'disks.qiniu.access_key' => $sc['QINIU_ACCESS_KEY'],
                'disks.qiniu.secret_key' => $sc['QINIU_SECRET_KEY'],
                'disks.qiniu.bucket' => $sc['QINIU_BUCKET'],
                'disks.qiniu.notify_url' => env('APP_EXTERNAL_URL') . '/web/qiniu/notify',
                'disks.qiniu.access' => 'public',
                'disks.qiniu.hotlink_prevention_key' => 'afc89ff8bd2axxxxxxxxxxxxxxbb',
                'disks.qiniu.sms_template_id' => $sc['QINIU_SMS_TEMPLATE_ID'],
            ]
        );
    }

    protected function boot()
    {
        $sc = sc('qiniu');
        Storage::extend(
            'qiniu',
            function ($app, $config) use ($sc) {
                $domains = [
                    'default' => $sc['QINIU_DOMAIN'],
                    'https' => null,
                    'custom' => null,
                ];

                $qiniu_adapter = new QiniuAdapter(
                    $sc['QINIU_ACCESS_KEY'],
                    $sc['QINIU_SECRET_KEY'],
                    $sc['QINIU_BUCKET'],
                    $domains,
                    env('APP_URL') . '/web/qiniu/notify',
                    'public',
                    'afc89ff8bd2axxxxxxxxxxxxxxbb'
                );
                $file_system = new Filesystem($qiniu_adapter);
                $file_system->addPlugin(new PrivateDownloadUrl());
                $file_system->addPlugin(new DownloadUrl());
                $file_system->addPlugin(new AvInfo());
                $file_system->addPlugin(new ImageInfo());
                $file_system->addPlugin(new ImageExif());
                $file_system->addPlugin(new ImagePreviewUrl());
                $file_system->addPlugin(new PersistentFop());
                $file_system->addPlugin(new PersistentStatus());
                $file_system->addPlugin(new UploadToken());
                $file_system->addPlugin(new PrivateImagePreviewUrl());
                $file_system->addPlugin(new VerifyCallback());
                $file_system->addPlugin(new Fetch());
                $file_system->addPlugin(new Qetag());
                $file_system->addPlugin(new WithUploadToken());
                $file_system->addPlugin(new LastReturn());

                return $file_system;
            }
        );
    }

    public function upload(string $photoPath, $source = null, $rename = "")
    {
        $disk = QiniuStorage::disk('qiniu');
        $now = Carbon::now();
        $currnetDate = $now->format('Y-m-d');
        $photoPath .= '/' . $currnetDate;
        if (is_string($source)) {
            $photoPath .= '/' . basename($source);
            $source = file_get_contents($source);
        }
        if ($rename) {
            $photoPath = $photoPath . '/' . $rename;
        }

        $photoPath = str_replace("//", "/", $photoPath);

        $res = $disk->put($photoPath, $source);
        if (is_bool($res) && $res) {
            $res = $photoPath;
        }
        if (!$res) {
            throw new \Exception("上传失败");
        }

        return $res;
    }

    public function delete($path)
    {
        $disk = QiniuStorage::disk('qiniu');
        return $disk->delete($path);
    }
    public function url($path)
    {
        $disk = QiniuStorage::disk('qiniu');
        return $disk->downloadUrl($path)->getUrl();
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
        $disk = QiniuStorage::disk('qiniu');
        return $disk->$method(...$parameters);
    }
}
