<?php
namespace App\Services;

use App\Models\GameBetRecord as GameBetRecordModel;
use App\Models\GameUserBeBankRecord as GameBankModel;
use App\Repositories\Interfaces\GameBetRecordRepository;
use App\Repositories\Interfaces\GameRepository;
use App\Repositories\Interfaces\GameUserBeBankRecordRepository;
use App\Repositories\Interfaces\GmAccountInfoRepository;
use App\Repositories\Interfaces\GmCfgServerListRepository;
use App\Repositories\Interfaces\GmCfgVipConfigRepository;
use App\Repositories\Interfaces\GmCfgVipPlayerRepository;
use App\Repositories\Interfaces\GmLogDailyRewardsRepository;
use App\Repositories\Interfaces\GmLogUserrequestRepository;
use App\Repositories\Interfaces\GmResVersionsRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Services\LiveRoom as LiveRoomService;
use App\Services\UserLevelService;
use App\Services\UserService;
use Carbon\Carbon;

//游戏服务
class GameService
{
    use \App\Traits\MagicGetTrait;
    private $userRepository;
    private $gameRepository;
    private $gameBetRecordRepository;
    private $gameUserBeBankRecordRepository;
    private $gmAccountInfoRepository;
    private $gmCfgServerListRepository;
    private $gmLogDailyRewardsRepository;
    private $gmCfgVipConfigRepository;
    private $gmCfgVipPlayerRepository;
    private $gmLogUserrequestRepository;
    private $gmResVersionsRepository;
    private $liveRoomRepository;

    private $liveRoomService;

    const TYPE = 7; //游戏类型
    const RES_VERSION = 1.2;
    const CHANNEL = 1000;
    const REG_GAME = 100;

    public function __construct(
        UserRepository $userRepository,
        GameRepository $gameRepository,
        GameBetRecordRepository $gameBetRecordRepository,
        GameUserBeBankRecordRepository $gameUserBeBankRecordRepository,
        GmAccountInfoRepository $gmAccountInfoRepository,
        GmCfgServerListRepository $gmCfgServerListRepository,
        GmLogDailyRewardsRepository $gmLogDailyRewardsRepository,
        GmCfgVipConfigRepository $gmCfgVipConfigRepository,
        GmCfgVipPlayerRepository $gmCfgVipPlayerRepository,
        GmLogUserrequestRepository $gmLogUserrequestRepository,
        LiveRoomRepository $liveRoomRepository,
        LiveRoomService $liveRoomService,
        GmResVersionsRepository $gmResVersionsRepository) {
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->gameBetRecordRepository = $gameBetRecordRepository;
        $this->gameUserBeBankRecordRepository = $gameUserBeBankRecordRepository;
        $this->gmAccountInfoRepository = $gmAccountInfoRepository;
        $this->gmCfgServerListRepository = $gmCfgServerListRepository;
        $this->gmLogDailyRewardsRepository = $gmLogDailyRewardsRepository;
        $this->gmCfgVipConfigRepository = $gmCfgVipConfigRepository;
        $this->gmCfgVipPlayerRepository = $gmCfgVipPlayerRepository;
        $this->gmLogUserrequestRepository = $gmLogUserrequestRepository;
        $this->gmResVersionsRepository = $gmResVersionsRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->liveRoomService = $liveRoomService;
    }

    /**
     * 存档
     *
     * @param    id                   $id   主键
     * @param    array                   $data 输入资料
     *
     * @return   void                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:24:25+0800
     */
    public function save($id, $data)
    {
        try {
            $data = $this->processCoverUpload($data);
            $game = $this->gameRepository->find($id);
            $originIosPhotoPath = $game->cover_ios;
            $originAndroidPhotoPath = $game->cover_android;

            $originRectangleIosPhotoPath = $game->rectangle_cover_ios;
            $originRectangleAndroidPhotoPath = $game->rectangle_cover_android;

            $originRoundPhotoPath = $game->round_cover;
            $return = $game->update($data);
            if (isset($data['cover_ios'])) {
                \CLStorage::delete(decodeStoragePath($originIosPhotoPath));
            }
            if (isset($data['cover_android'])) {
                \CLStorage::delete(decodeStoragePath($originAndroidPhotoPath));
            }

            if (isset($data['rectangle_cover_ios'])) {
                \CLStorage::delete(decodeStoragePath($originRectangleIosPhotoPath));
            }
            if (isset($data['rectangle_cover_android'])) {
                \CLStorage::delete(decodeStoragePath($originRectangleAndroidPhotoPath));
            }

            if (isset($data['round_cover'])) {
                \CLStorage::delete(decodeStoragePath($originRoundPhotoPath));
            }
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 处理图片上传
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   array                   返回上传成功的输入资料
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:25:31+0800
     */
    protected function processCoverUpload($data)
    {
        if (isset($data['cover_ios'])) {
            $ext = $data['cover_ios']->getClientOriginalExtension();
            $photoPath = $this->gameRepository->makeModel()::COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['cover_ios']);
            $data['cover_ios'] = $url;
        }
        if (isset($data['cover_android'])) {
            $ext = $data['cover_android']->getClientOriginalExtension();
            $photoPath = $this->gameRepository->makeModel()::COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['cover_android']);
            $data['cover_android'] = $url;
        }
        if (isset($data['round_cover'])) {
            $ext = $data['round_cover']->getClientOriginalExtension();
            $photoPath = $this->gameRepository->makeModel()::ROUND_COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['round_cover']);
            $data['round_cover'] = $url;
        }
        if (isset($data['rectangle_cover_android'])) {
            $ext = $data['rectangle_cover_android']->getClientOriginalExtension();
            $photoPath = $this->gameRepository->makeModel()::RECTANGLE_COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['rectangle_cover_android']);
            $data['rectangle_cover_android'] = $url;
        }
        if (isset($data['rectangle_cover_ios'])) {
            $ext = $data['rectangle_cover_ios']->getClientOriginalExtension();
            $photoPath = $this->gameRepository->makeModel()::RECTANGLE_COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['rectangle_cover_ios']);
            $data['rectangle_cover_ios'] = $url;
        }

        return $data;
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   RollAd                         新增成功的广告
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        $data = $this->processCoverUpload($data);
        return $this->gameRepository->create($data);
    }

    public function allGames($columns = ['*'])
    {
        $offset = 1;
        $model = app($this->gameRepository->model());
        $where = ['status' => $model::STATUS_ENABLE];
        $orderByColumn = 'created_at';
        $result = $this->gameRepository->scopeQuery(function ($query) use ($orderByColumn) {
            return $query->orderBy($orderByColumn, 'asc');
        })->findWhere($where, $columns)
        ;
        return $result;
    }

    /**
     * 快速註冊與登入
     */
    public function quickRegisterAndLogin($userId, $nickname, $token, $resversion, $channel, $regGame, $devId, $simSerial, $osver, $appver, $lineNo)
    {
        $logintype = 'quick';
        $keyword = $logintype . 'Register';

        $realip = $this->getUserIP();
        $now = Carbon::now();

        $accountModel = $this->getGmAccountModelByOpuuid($userId);
        // 如果帳號不存在, 創建一組帳號
        if ($accountModel == null) {
            $keyword = $logintype . 'Register';
            $accountModel = $this->register($userId, $nickname, $token, $devId, $channel, $regGame, $realip, $now);
        } else {
            $keyword = $logintype . 'quickLogin';

            // 檢查帳號是否被封號
            if ($this->checkBlock($accountModel->block_start, $accountModel->block_end)) {
                throw new \Exception(__('message.account_block'), \App\Exceptions\ErrorCode::TOKEN_ERROR);
            }
            // 更新登入的相關資訊
            $accountModel->last_login = $now;
            $accountModel->ip_last = $realip;
            $accountModel->token = $token;
            $accountModel->token_invalid = $now->addDay();
            $accountModel->save();
        }
        $this->saveLogin($resversion, $keyword, $accountModel->gid, $accountModel->op_uuid, $channel, $realip, $devId, $simSerial, $osver, $appver, $lineNo);
    }

    /**
     * 註冊
     */
    public function register($userId, $nickname, $token, $devId, $channel, $regGame, $realip = null, $now = null)
    {

        if ($realip == null) {
            $realip = $this->getUserIP();
        }
        if ($now == null) {
            $now = Carbon::now();
        }

        if ($regGame == "") {
            $regGame = self::REG_GAME;
        }

        if ($devId == "") {
            $devId = $userId;
        }

        // 準備要寫入db的資料
        $user = [];
        $user['gid'] = $userId;
        $user['account_name'] = $nickname;
        $user['account_pwd'] = "NoPasswd";
        $user['device_id'] = $devId; // outside
        $user['op_uuid'] = $userId;
        $user['reg_type'] = self::TYPE;
        $user['reg_channel'] = strval($channel); // outside
        $user['reg_game'] = $regGame; // outside
        $user['reg_time'] = $now;
        $user['ip_reg'] = $realip;
        $user['last_login'] = $now;
        $user['ip_last'] = $realip;
        $user['token'] = $token;
        $user['status'] = 0;
        $user['token_invalid'] = $now->addDay();

        // 寫入db並回傳model
        return $this->gmAccountInfoRepository->create($user);
    }

    /**
     * 檢查遊戲資訊
     */
    public function checkGameVersionInfo($regGame, $devId, $uuid)
    {

        $result = [];

        if ($devId == "") {
            $devId = $uuid;
        }

        $normalFlag = 1;
        $betaFlag = 2;

        $realip = $this->getUserIP();

        $versionModel = $this->getResVersions($regGame, $normalFlag);
        $betaVersionModel = $this->getResVersions($regGame, $betaFlag);
        if ($versionModel == null) {
            return [];
        }

        // 檢查是否在beta測試白名單內
        if ($betaVersionModel != null) {
            if ($versionModel == null || $betaVersionModel->id > $versionModel->id) {
                $versionModel = $betaVersionModel;
            }
        }

        $needDownload = 0;

        $result['isreview'] = 0;

        $result['needDownload'] = $needDownload;
        $result['asseturl'] = $versionModel->asseturl;
        $result['gameid'] = $versionModel->gameid;
        $result['res_ver'] = $versionModel->res_ver;
        return $result;
    }

    /**
     * 取得遊戲server資訊
     */
    public function getGameServerInfo()
    {
        return $this->getServerInfo();
    }

    /**
     * 用 opuuid 取得 gm account model
     */
    protected function getGmAccountModelByOpuuid($opuuId)
    {
        $collection = $this->gmAccountInfoRepository->findWhere(['op_uuid' => $opuuId]);
        return $collection->first();
    }

    public function betJob($betRecordId, $betGold, $userId, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        $betRecordModel = $this->gameBetRecordRepository->findWhere(
            [
                'id' => $betRecordId,
            ]
        )->first();
        if ($betRecordModel == null) {
            return;
        }

        // 減少用戶金幣數量
        $userGold = $userModel->gold - $betGold;
        $this->userRepository->addGold($userModel, -1 * $betGold, $betRecordModel);

        // 增加可提現額度
        $userModel->real_withdraw_gold += $betGold;
        $userModel->save();
        //增加經驗
        app(UserLevelService::class)->addExpByGameBet($userModel->id, $betGold);

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);
    }

    public function betExistJob($betRecordId, $betGold, $userId, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        $betRecordModel = $this->gameBetRecordRepository->findWhere(
            [
                'id' => $betRecordId,
            ]
        )->first();
        if ($betRecordModel == null) {
            return;
        }

        // 累加下注金幣數量
        $totalBetGoldgold = $betRecordModel->bet_gold + $betGold;
        $betRecordModel->bet_gold = $totalBetGoldgold;
        $betRecordModel->save();

        // 減少用戶金幣數量
        $userGold = $userModel->gold - $betGold;
        $this->userRepository->addGold($userModel, -1 * $betGold, $betRecordModel);

        // 增加可提現額度
        $userModel->real_withdraw_gold += $betGold;
        $userModel->save();

        //增加經驗
        app(UserLevelService::class)->addExpByGameBet($userModel->id, $betGold);

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);
    }

    /**
     * 遊戲下注
     */
    public function bet($userId, $betGold, $gameSlug, $gameRound, $orderId)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();
        //透過 userId 取得加入那間房間
        $roomRecord = $this->liveRoomService->getCurrentRoomRecordByUserId($userId);
        $roomId = $roomRecord['room_id'] ?? 0;
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user'));
        }
        $userCacheGold = $userModel->gold_cache;
        if ($userCacheGold < $betGold) {
            throw new \Exception(__('user.gold_not_enough'));
        }

        if ($orderId != '') {
            // 依照order id 取得 bet model
            $betRecordModel = $this->gameBetRecordRepository->findWhere(
                [
                    'id' => $orderId,
                    'user_id' => $userId,
                ]
            )->first();

            // 注單號不存在
            if ($betRecordModel == null) {
                throw new \Exception(__('bet.order_id_not_found'));
            }
            // 注單已結算
            if ($betRecordModel->status != GameBetRecordModel::STATUS_WAIT) {
                throw new \Exception(__('bet.order_already_settled'));
            }

            // 累加下注金幣數量
            $totalBetGoldgold = $betRecordModel->bet_gold + $betGold;

            $diffGold = -1 * $betGold;
            $this->userRepository->addGold($userModel, $diffGold, null, true, function ($cacheKey, $fieldKey) use ($betRecordModel, $betGold, $userModel) {
                \Queue::pushOn(pool('bet'), new \App\Jobs\GameBetExist($betRecordModel->id, $betGold, $userModel->id, $cacheKey, $fieldKey));
            });
            $userCacheGold = $userCacheGold - $betGold;
            return [
                'remain_gold' => $userCacheGold,
                'total_bet_gold' => $totalBetGoldgold,
                'order_id' => $betRecordModel->id,
            ];
        }

        // 新增一筆注單資料
        $betData = [
            'user_id' => $userId,
            'channel' => $userModel->register_channel,
            'bet_gold' => $betGold,
            'game_slug' => $gameSlug,
            'game_round' => $gameRound,
            'room_id' => $roomId,
            'status' => GameBetRecordModel::STATUS_WAIT,
            'win_gold' => 0,
        ];
        $gameBetRecordModel = $this->gameBetRecordRepository->create($betData);

        $diffGold = -1 * $betGold;
        $userCacheGold = $userCacheGold - $betGold;

        $this->userRepository->addGold($userModel, $diffGold, null, true, function ($cacheKey, $fieldKey) use ($gameBetRecordModel, $betGold, $userModel) {
            \Queue::pushOn(pool('bet'), new \App\Jobs\GameBet($gameBetRecordModel->id, $betGold, $userModel->id, $cacheKey, $fieldKey));
        });

        return [
            'remain_gold' => $userCacheGold,
            'total_bet_gold' => $betGold,
            'order_id' => $gameBetRecordModel->id,
        ];
    }

    /**
     * 訂單派彩job
     */
    public function betSettledJob($status, $betRecordId, $userId, $winGold, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        $betRecordModel = $this->gameBetRecordRepository->findWhere(
            [
                'id' => $betRecordId,
            ]
        )->first();
        if ($betRecordModel == null) {
            return;
        }

        // 更新注單狀態
        if ($status == GameBetRecordModel::STATUS_WIN) {
            $betRecordModel->win_gold = $winGold;
            $betRecordModel->status = GameBetRecordModel::STATUS_WIN;
        } else {
            $betRecordModel->win_gold = 0;
            $betRecordModel->status = GameBetRecordModel::STATUS_LOSE;
        }
        $betRecordModel->save();

        // 統計直播間內的下注與中獎金額
        $roomId = $betRecordModel->room_id;
        $roomModel = $this->liveRoomRepository->findWhere(['id' => $roomId])->first();
        if ($roomModel != null) {
            $roomModel->total_game_bet += $betRecordModel->bet_gold;
            $roomModel->total_game_win += $betRecordModel->win_gold;
            $roomModel->save();
        }

        $userGold = $userModel->gold;
        // 增加用戶金幣數量
        if ($status == GameBetRecordModel::STATUS_WIN) {
            $userGold = $userModel->gold + $winGold;
            $this->userRepository->addGold($userModel, $winGold, $betRecordModel);
        }

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);
    }

    /**
     * 用戶訂單派彩
     */
    public function betSettled($userId, $orderId, $status, $winGold)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user'));
        }

        // 依照order id 取得 bet model
        $betRecordModel = $this->gameBetRecordRepository->findWhere(
            [
                'id' => $orderId,
                'user_id' => $userId,
            ]
        )->first();

        // 注單號不存在
        if ($betRecordModel == null) {
            throw new \Exception(__('bet.order_id_not_found'));
        }
        // 注單已結算
        if ($betRecordModel->status != GameBetRecordModel::STATUS_WAIT) {
            throw new \Exception(__('bet.order_already_settled'));
        }

        $this->userRepository->addGold($userModel, $winGold, null, true, function ($cacheKey, $fieldKey) use ($status, $betRecordModel, $winGold, $userModel) {
            \Queue::pushOn(pool('bet'), new \App\Jobs\GameSettled($status, $betRecordModel->id, $userModel->id, $winGold, $cacheKey, $fieldKey));
        });

        $userCacheGold = $userModel->gold_cache;
        if ($status == GameBetRecordModel::STATUS_WIN) {
            $userCacheGold = $userCacheGold + $winGold;
        }

        return $userCacheGold;
    }

    /**
     * 用戶上莊
     */
    public function bankOn($userId, $gameSlug, $bankGold)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user'));
        }
        $userCacheGold = $userModel->gold_cache;
        if ($userCacheGold < $bankGold) {
            throw new \Exception(__('user.gold_not_enough'));
        }
        //透過 userId 取得加入那間房間
        $roomRecord = $this->liveRoomService->getCurrentRoomRecordByUserId($userId);
        $roomId = $roomRecord['room_id'] ?? 0;

        // 新增一筆上莊資料
        $gameBankData = [
            'user_id' => $userId,
            'room_id' => $roomId,
            'status' => GameBankModel::ON_STATUS,
            'channel' => $userModel->register_channel,
            'game_slug' => $gameSlug,
            'bank_on_gold' => $bankGold,
            'bank_off_gold' => 0,
        ];
        $gameBankRecordModel = $this->gameUserBeBankRecordRepository->create($gameBankData);

        // 減少用戶金幣數量
        $userGold = $userModel->gold - $bankGold;
        $this->userRepository->addGold($userModel, -1 * $bankGold, $gameBankRecordModel);

        // 回傳cache的金幣數量
        $userCacheGold = $userCacheGold - $bankGold;
        return [
            'remain_gold' => $userCacheGold,
            'bank_id' => $gameBankRecordModel->id,
        ];
    }

    /**
     * 下莊
     */
    public function bankOff($gameBankId, $bankGold)
    {
        $gameBankRecordModel = $this->gameUserBeBankRecordRepository->findWhere(['id' => $gameBankId])->first();
        if ($gameBankRecordModel == null) {
            throw new \Exception(__('game.bank_id_not_found'));
        }
        if ($gameBankRecordModel->status != GameBankModel::ON_STATUS) {
            throw new \Exception(__('game.already_bank_off'));
        }
        // 更新玩家當莊家紀錄
        $gameBankRecordModel->status = GameBankModel::OFF_STATUS;
        $gameBankRecordModel->bank_off_gold = $bankGold;
        $gameBankRecordModel->save();

        $userId = $gameBankRecordModel->user_id;
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

        // 增加用戶金幣數量
        $userGold = $userModel->gold + $bankGold;
        $this->userRepository->addGold($userModel, $bankGold, $gameBankRecordModel);

        // 回傳cache的金幣數量
        $userCacheGold = $userModel->gold_cache;
        $userCacheGold = $userCacheGold + $bankGold;
        return [
            'remain_gold' => $userModel->gold_cache,
        ];
    }

    /**
     * 取得用戶金幣資料
     */
    public function getUserGold($id)
    {
        $userService = app(UserService::class);
        // 讀取快取資料
        return $userService->getUserGoldCacheById($id);
    }

    /**
     * 取得發送request的用戶ip
     */
    protected function getUserIP()
    {

        if (isset($_SERVER['HTTP_VIA']) && isset($_SERVER['HTTP_ALI_CDN_REAL_IP'])) {
            return $_SERVER['HTTP_ALI_CDN_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_IP'])) {
            return $_SERVER['HTTP_X_FORWARDED_IP'];
        } else {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        }
    }

    /**
     * 是否在被封帳號的時間內
     */
    protected function checkBlock($block_start, $block_end)
    {
        $now = Carbon::now();
        if ($block_end >= $now && $block_start <= $now) {
            // 被封號
            return true;
        } else {
            //沒有被封號
            return false;
        }
    }

    /**
     * 從server list資料表取得遊戲server資訊
     */
    protected function getServerInfo()
    {
        $serverinfo = [];

        $serverListConfigCollection = $this->gmCfgServerListRepository->findWhere(['status' => 0, 'server_type' => 0]);
        $serverListConfigModel = $serverListConfigCollection->first();
        if ($serverListConfigModel != null) {
            $serverinfo['servip'] = $serverListConfigModel->serverip;
            $serverinfo['servport'] = strval($serverListConfigModel->serverport);
            $serverinfo['servid'] = $serverListConfigModel->serverid;
        }
        return $serverinfo;
    }

    /**
     * 取得每日資訊
     */
    protected function getDailyInfo($gid)
    {
        $inf = [];
        $inf['szVipRatio'] = 1;

        // 用gid讀取每日獎勵紀錄
        $dailyRewardLogCollection = $this->gmLogDailyRewardsRepository->scopeQuery(function ($query) {
            return $query->orderBy('id', 'desc');
        })->findWhere(['gid' => $gid]);
        $dailyRewardLogModel = $dailyRewardLogCollection->first();

        //var_dump($dailyRewardLogModel);exit;
        // 取出vip玩家設定資料
        $vipPlayerConfigCollection = $this->gmCfgVipPlayerRepository->findWhere(
            [
                'userid' => $gid,
                'is_use' => 1,
                ['expire_at', '>', Carbon::now()],
            ]
        );
        $vipPlayerConfigModel = $vipPlayerConfigCollection->first();

        // 依照vip玩家設定, 取出詳細的vip設定
        $vipConfigModel = null;
        if ($vipPlayerConfigModel != null) {
            $vipConfigCollection = $this->gmCfgVipConfigRepository->findWhere(['id' => $vipPlayerConfigModel->vipid]);
            $vipConfigModel = $vipConfigCollection->first();
        }
        if ($vipConfigModel != null) {
            $inf['szVipRatio'] = $vipConfigModel->checkinreward;
        }
        // 設置預設inf資料
        $inf['isFirst'] = 1;
        $inf['szRatioDays'] = '0';
        $inf['nextRatioDays'] = '0.1';
        $inf['tilldays'] = intval(0);
        $inf['nextSilldays'] = 1;

        if ($dailyRewardLogModel != null) {
            $dailyRewardLogArray = $dailyRewardLogModel->toArray();

            if (date('Y-m-d', strtotime($dailyRewardLogArray['ctime'])) == date("Y-m-d")) {
                //今天领取过
                $inf['isFirst'] = 0;
                $inf['nextSilldays'] = intval($dailyRewardLogArray['straight']) + 1;
                $inf['tilldays'] = intval($dailyRewardLogArray['straight']);
                if (is_array($ars) && count($ars) > 0 && $dailyRewardLogArray['straight'] >= 10) {
                    $dailyRewardLogArray['straight'] = 10;
                }
                $inf['szRatioDays'] = strval(0.1 * $dailyRewardLogArray['straight']);
                $inf['nextRatioDays'] = strval(0.1 * ($dailyRewardLogArray['straight'] >= 9 ? 10 : ($dailyRewardLogArray['straight'] + 1)));
            } else if (date('Y-m-d', strtotime($dailyRewardLogArray['ctime'])) == date("Y-m-d", strtotime("-1 day"))) {
                //昨天登录过
                $inf['isFirst'] = 1;
                $inf['tilldays'] = intval($dailyRewardLogArray['straight'] + 1);
                $inf['nextSilldays'] = intval($dailyRewardLogArray['straight']) + 1;
                if (is_array($ars) && count($ars) > 0 && $dailyRewardLogArray['straight'] >= 10) {
                    $dailyRewardLogArray['straight'] = 10;
                }
                $inf['szRatioDays'] = strval(0.1 * $dailyRewardLogArray['straight']);
                $inf['nextRatioDays'] = strval(0.1 * ($dailyRewardLogArray['straight'] >= 9 ? 10 : ($dailyRewardLogArray['straight'] + 1)));
            }
        }

        return $inf;
    }

    /**
     * 將登入資訊寫入log
     */
    protected function saveLogin($resversion, $keyword, $gid, $uuid, $channel, $ip, $devId, $simSerial, $osver, $appver, $lineNo)
    {
        $logintime = date('Y-m-d H:i:s');

        $data = [
            'gid' => $gid,
            'keyword' => $gid,
            'resversion' => $gid,
            'osver' => $osver,
            'appver' => $appver,
            'lineNo' => $lineNo,
            'uuid' => $uuid,
            'simSerial' => $simSerial,
            'dev_id' => $devId,
            'channel' => $channel,
            'ctime' => $logintime,
            'request_ip' => $ip,
            'city' => '无',
            'city_id' => '0',
            'isp' => '无',
        ];
        $this->gmLogUserrequestRepository->create($data);
    }

    /**
     * 取得 res version
     */
    protected function getResVersions($regGame, $flag)
    {
        $versionCollection = $this->gmResVersionsRepository->scopeQuery(function ($query) {
            return $query->orderBy('id', 'desc');
        })->findWhere([
            'gameid' => $regGame,
            'is_release' => $flag,
        ]);

        $versionModel = $versionCollection->first();
        return $versionModel;
    }

    /**
     * 处理图片上传
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   array                   返回上传成功的输入资料
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:25:31+0800
     */
    public function processCoverUploadByLocalFile($file)
    {
        $photoPath = $this->gameRepository->makeModel()::COVER_PATH_PREFIX;
        $url = \CLStorage::upload($photoPath, $file);
        return $url;
    }
}
