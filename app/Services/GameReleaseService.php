<?php
namespace App\Services;

use App\Repositories\Interfaces\GameReleaseRepository;

/**
 * 游戏版本服务
 */
class GameReleaseService
{
    use \App\Traits\MagicGetTrait;
    const GAME_RELEASE_KEY = 'release:game_data';

    private $gameReleaseRepository;
    public function __construct(GameReleaseRepository $gameReleaseRepository)
    {
        $this->gameReleaseRepository = $gameReleaseRepository;
    }

    /**
     * 新增一筆遊戲版本
     */
    public function create($slug, $version, $asset, $comment)
    {
        $url = \Storage::disk('local')->put('game/' . $slug . '/' . $version, $asset);

        $data = [
            'game_slug' => $slug,
            'is_on' => 0,
            'version' => $version,
            'local_download_url' => $url,
            'comment' => $comment,
        ];

        $record = $this->gameReleaseRepository->create($data);

        $this->syncData($record);
    }

    /**
     * 更新一筆資料
     */
    public function update($parameters)
    {
        $record = $this->gameReleaseRepository->findWhere(['id' => $parameters['game_version_id']]);
        if ($record->count() == 0) {
            throw new \Exception('invalid game version id');
        }

        $record = $record->first();
        $record->game_slug = isset($parameters['game_slug']) ? $parameters['game_slug'] : $record->game_slug;
        $record->version = isset($parameters['version']) ? $parameters['version'] : $record->version;

        if (isset($parameters['local_download_url'])) {
            $url = \Storage::disk('local')->put('game/' . $record->game_slug . '/' . $record->version, $parameters['local_download_url']);
            $record->local_download_url = $url;
        }

        $record->comment = isset($parameters['comment']) ? $parameters['comment'] : $record->comment;

        $record->save();
        $this->syncData($record);
    }

    /**
     * 遊戲發布
     */
    public function release($gameReleaseId)
    {
        $releaseModel = $this->gameReleaseRepository->findWhere(['id' => $gameReleaseId])->first();
        if ($releaseModel == null) {
            return;
        }
        $slug = $releaseModel->game_slug;

        $modelArray = $this->gameReleaseRepository->findWhere(['game_slug' => $slug])->all();
        $length = count($modelArray);

        for ($i = 0; $i < $length; $i++) {
            $model = $modelArray[$i];
            if ($model->id == $releaseModel->id) {
                continue;
            }
            if ($model->is_on == 1) {
                $model->is_on = 0;
                $model->save();
            }
        }

        $releaseModel->is_on = 1;
        $releaseModel->cdn_url = cdnUrl($releaseModel->remote_url);
        $releaseModel->save();
        \Queue::pushOn(pool('default'), new \App\Jobs\CDNPrefetch($releaseModel->cdn_url));
    }
    // 將檔案同步到官方網站
    public function syncData($releaseModel)
    {

        if (env('APP_RELEASE_HOST_USER') != '' && env('APP_RELEASE_HOST') != '' && env('APP_GAME_RELEASE_DIR_ROOT') != '') {

            $localPath = storage_path('app/' . $releaseModel->local_download_url);

            $targetHostUser = env('APP_RELEASE_HOST_USER');
            $targetHost = env('APP_RELEASE_HOST');
            $targetFilePath = env('APP_GAME_RELEASE_DIR_ROOT') . $releaseModel->game_slug . '.zip';

            $command = 'rsync -avh ' . $localPath . ' ' . $targetHostUser . '@' . $targetHost . ':' . $targetFilePath;

            shell_exec($command);
        }
    }
    public function getAllRelease()
    {
        $gameData = $this->getGameReleaseDataFromCache();
        if (true || $gameData == null && $gameData == []) {
            $gameData = $this->getGameReleaseDataFromDB();
            $this->writeGameReleaseDataToCache($gameData);
        }

        $result['game'] = $gameData;
        foreach ($result['game'] as $key => $data) {
            // $result['game'][$key]['download_url'] = $data['remote_url'] ?? '';
            $result['game'][$key]['download_url'] = $data['cdn_url'] ? $data['cdn_url'] : cdnUrl($data['remote_url']) ?? "";
        }
        return $result;
    }

    public function writeGameReleaseDataToCache($data)
    {
        $key = self::GAME_RELEASE_KEY;

        \Cache::forever($key, $data);
    }

    /**
     * 從cache取得已game發布的資料
     */
    public function getGameReleaseDataFromCache()
    {
        $key = self::GAME_RELEASE_KEY;

        return \Cache::get($key);
    }

    /**
     * 從db取得game已發布的資料
     */
    public function getGameReleaseDataFromDB()
    {

        $gameModelArray = $this->gameReleaseRepository->findWhere(['is_on' => 1], ['game_slug', 'version', 'cdn_url'])->all();
        return $gameModelArray;
    }

}
