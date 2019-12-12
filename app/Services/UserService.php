<?php
namespace App\Services;

use App\Models\AnnouceForUser as AnnouceForUserModel;
use App\Models\LiveRoom as LiveRoomModel;
use App\Models\User as UserModel;
use App\Models\LiveSchedule as LiveScheduleModel;
use App\Models\UserAuth;
use App\Models\UserAvatarChangeTimes as UserAvatarChangeTimesModel;
use App\Models\UserConfig;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\AnnouceForUserRepository;
use App\Repositories\Interfaces\GmAccountInfoRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\RealNameVerifyRepository;
use App\Repositories\Interfaces\UserAuthRepository;
use App\Repositories\Interfaces\UserAvatarChangeTimesRepository;
use App\Repositories\Interfaces\UserChatBlackListRepository;
use App\Repositories\Interfaces\UserConfigRepository;
use App\Repositories\Interfaces\UserFeedbackRepository;
use App\Repositories\Interfaces\UserFollowRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\UserStoryWallRepository;
use App\Repositories\Interfaces\LiveScheduleRepository;
use App\Services\AnchorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Exceptions\Code;

//用户服务
class UserService
{
    use \App\Traits\MagicGetTrait;
    private $userRepository;
    private $userAuthRepository;
    private $userFollowRepository;
    private $realNameVerifyRepository;
    private $managerRepository;
    private $userConfigRepository;
    private $userAvatarChangeTimesRepository;
    private $anchorInfoRepository;
    private $userFeedbackRepository;
    private $userChatBlackListRepository;
    private $liveRoomRepository;
    private $liveScheduleRepository;
    private $userStoryWallRepository;
    private $gmAccountInfoRepository;
    private $annouceForUserRepository;

    const DEFAULT_USER_NICKNAME_PREFIX = 'GSC_';
    const DEFAULT_IMAGE_EXTENSION = 'png';
    const DEFAULT_AVATAR_FILENAME_PREFIX = 'avatar';
    const DEFAULT_AVATAR_ROOT_DIRCTORY = 'avatar/';

    const DEFAULT_FRONTCOVER_FILENAME_PREFIX = 'frontcover';
    const DEFAULT_FRONTCOVER_ROOT_DIRCTORY = 'frontcover/';

    const DEFAULT_STORY_PHOTO_FILENAME_PREFIX = 'story_photo';
    const DEFAULT_STORY_PHOTO_ROOT_DIRCTORY = 'story_photo/';

    const DEFAULT_STORY_MAIN_PHOTO_FILENAME_PREFIX = 'story_main_photo';
    const DEFAULT_STORY_MAIN_PHOTO_ROOT_DIRCTORY = 'story_main_photo/';


    public function __construct(UserRepository $userRepository,
        UserAuthRepository $userAuthRepository,
        UserFollowRepository $userFollowRepository,
        RealNameVerifyRepository $realNameVerifyRepository,
        ManagerRepository $managerRepository,
        UserConfigRepository $userConfigRepository,
        UserAvatarChangeTimesRepository $userAvatarChangeTimesRepository,
        AnchorInfoRepository $anchorInfoRepository,
        UserFeedbackRepository $userFeedbackRepository,
        UserChatBlackListRepository $userChatBlackListRepository,
        LiveRoomRepository $liveRoomRepository,
        LiveScheduleRepository $liveScheduleRepository,
        UserStoryWallRepository $userStoryWallRepository,
        AnnouceForUserRepository $annouceForUserRepository,
        GmAccountInfoRepository $gmAccountInfoRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userAuthRepository = $userAuthRepository;
        $this->userFollowRepository = $userFollowRepository;
        $this->realNameVerifyRepository = $realNameVerifyRepository;
        $this->managerRepository = $managerRepository;
        $this->userConfigRepository = $userConfigRepository;
        $this->userAvatarChangeTimesRepository = $userAvatarChangeTimesRepository;
        $this->anchorInfoRepository = $anchorInfoRepository;
        $this->userFeedbackRepository = $userFeedbackRepository;
        $this->userChatBlackListRepository = $userChatBlackListRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->liveScheduleRepository = $liveScheduleRepository;
        $this->userStoryWallRepository = $userStoryWallRepository;
        $this->gmAccountInfoRepository = $gmAccountInfoRepository;
        $this->annouceForUserRepository = $annouceForUserRepository;

    }

    /**
     * 用id從db取出用戶資料
     * @param string $id
     * @return Collection
     */
    public function findById(string $id)
    {
        $data = array();
        $result = $this->userRepository->findWhere(['id' => $id]);
        return $result;
    }

    /**
     * 用cellphone從db取出用戶資料
     *
     * @param string $cellphone
     *
     * @return Collection
     */
    public function findByCellphone(string $cellphone)
    {
        $data = array();
        $result = $this->userRepository->findByField('cellphone', $cellphone);
        return $result;
    }

    /**
     * 用 nickname 從db取出用戶資料
     *
     * @param string $nickname
     *
     * @return Collection
     */
    public function findByNickname(string $nickname)
    {
        $data = array();
        $result = $this->userRepository->findByField('nickname', $nickname);
        return $result;
    }

    /**
     * 新增一用戶資料
     *
     * @param array $parameters
     *
     * @return Collection
     */
    public function create(array $parameters)
    {
        $data = [
            'nickname' => static::DEFAULT_USER_NICKNAME_PREFIX . $parameters['cellphone'],
            'cellphone' => $parameters['cellphone'],
            'avatar' => config('app.avatar'),
            'sign' => '',
            'register_channel' => $parameters['channel'],
            'register_uuid' => $parameters['uuid'],
            'register_device_type' => $parameters['device_type'],
            'register_os_version' => $parameters['os_version'],
            'password' => bcrypt($parameters['password']),
        ];

        // 新增 user 資料
        $user = $this->userRepository->create($data);

        // 將user資料的暱稱改為 prefix + id
        $user->nickname = $this->defaultNickName($user);

        // 儲存修改暱稱
        $user->save();

        return $user;
    }

    /**
     * 更新用户资料
     *
     * @param    integer                   $id   用户id
     * @param    array                   $data 输入资料
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-04T11:07:47+0800
     */
    public function editUser($id, $data)
    {
        if (isset($data['currentLevel'])) {
            $data['level'] = getWhichExpByLevel($data['currentLevel']);
        }
        $this->userRepository->update($data, $id);
    }
    /**
     * 用手機號碼來檢查用戶是否存在
     *
     * @param string $cellphone 手機號碼
     *
     * @return bool
     */
    public function checkExistByCellphone(string $cellphone)
    {
        if ($this->findByCellphone($cellphone)->first() != null) {
            return true;
        }
        return false;
    }

    /**
     * 修改用戶暱稱
     *
     * @param string $id 用戶id
     * @param string $nickname 用戶暱稱
     */
    public function editNickname($id, $nickname)
    {
        // 從 db 找出 相同 nickname 的用戶
        $userCollection = $this->userRepository->findWhere(
            [
                'nickname' => $nickname,
                ['id', '<>', $id],
            ]
        );

        // 若nickname已被使用, 回傳錯誤
        $userModel = $userCollection->first();
        if ($userModel != null) {
            throw new \Exception(__('user.nickname_exist', ['nickname' => $nickname]));
        }

        // 若nickname使用了系統預設的字符串, 回傳錯誤
        if ($this->checkNicknameNotUseSystemName($id, $nickname) == false) {
            throw new \Exception(__('user.nickname_sysytem_used', ['nickname' => $nickname]));
        }

        // 用id找出相對應的用戶
        $userCollection = $this->findById($id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }

        // 修改遊戲暱稱
        $accountModel = $this->gmAccountInfoRepository->findWhere(['op_uuid' => $id])->first();
        $accountModel->account_name = $nickname;
        $accountModel->save();

        //更新暱稱資料
        $userModel->nickname = $nickname;
        $userModel->save();
    }

    /**
     * 修改用戶性別
     *
     * @param string $id 用戶id
     * @param string $sex 用戶性別
     */
    public function editSex($id, $sex)
    {
        // 用id找出相對應的用戶
        $userCollection = $this->findById($id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }

        // 更新性別資料
        $userModel->sex = $sex;
        $userModel->save();
    }

    /**
     * 修改用戶簡介
     *
     * @param string $id 用戶id
     * @param string $intro 用戶簡介
     */
    public function editIntro($id, $intro)
    {
        $userModel = $this->userRepository->findWhere(['id' => $id])->first();
        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }

        // 儲存簡介
        $userModel->intro = $intro;
        $userModel->save();
    }

    /**
     * 修改用戶生日
     *
     * @param string $id 用戶id
     * @param string $birthday 用戶生日
     */
    public function editBirthday($id, $birthday)
    {
        // 用id找出相對應的用戶
        $userCollection = $this->findById($id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }

        // 更新生日資料
        $userModel->birthday = $birthday;
        $userModel->save();
    }

    /**
     * 修改用戶簽名
     *
     * @param string $id 用戶id
     * @param string $sign 用戶簽名
     */
    public function editSign($id, $sign)
    {
        // 用id找出相對應的用戶
        $userCollection = $this->findById($id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }

        // 更新簽名資料
        $userModel->sign = $sign;
        $userModel->save();
    }

    /**
     * 取得主播封面圖的token與上傳位置
     *
     * @param int|string $user_id
     *
     * @return array
     */
    public function getFrontcoverTokenAndFilePath($user_id)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($user_id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }
        // 若用戶不是主播, 回傳錯誤
        if ($userModel->user_type_id != UserModel::USER_TYPE_ANCHOR) {
            throw new \Exception(__('user.is_not_anchor', ['user' => $user_id]));
        }

        // 依照用戶user_id組成, 上傳檔案的路徑
        $fullPathName = $this->getFrontcoverUploadFilePath($user_id);

        // 取得token
        $token = \CLStorage::getDriver()->uploadToken($fullPathName, 3600, ['insertOnly' => 0], true);

        $result = [
            'token' => $token,
            'file_path' => $fullPathName,
        ];
        return $result;
    }

    /**
     * 設定主播封面圖url
     *
     * @param int $user_id
     * @param string $url
     */
    public function setFrontcoverUrl($user_id, $url)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($user_id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }

        // 若用戶不是主播, 回傳錯誤
        if ($userModel->user_type_id != UserModel::USER_TYPE_ANCHOR) {
            throw new \Exception(__('user.is_not_anchor', ['user' => $user_id]));
        }

        $anchorInfoCollection = $this->anchorInfoRepository->findByField('user_id', $user_id);
        $anchorInfoModel = $anchorInfoCollection->first();

        //將url資料寫入db
        $anchorInfoModel->front_cover = $url;
        $anchorInfoModel->save();

        // if($anchorInfoModel != null) {
        //     $anchorInfoModel->fron_cover = $url;
        //     $anchorInfoModel->save();
        // }

        return $anchorInfoModel->front_cover;
    }

    /**
     * 取得上傳 avatar 的 token 與 圖片上傳路徑
     *
     * @param int $user_id
     */
    public function getAvatarTokenAndFilePath($user_id)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($user_id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }

        // 從db找出用戶的可更換頭像次數
        $userAvatarChangeTimesModel = $this->userAvatarChangeTimesRepository->findWhere([
            'user_id' => $user_id,
        ]);
        $userAvatarChangeTimesCollection = $userAvatarChangeTimesModel->first();

        // 檢查是否可以更新頭像
        if ($this->checkAvatarCanChange($userAvatarChangeTimesCollection) == false) {
            throw new \Exception(__('user.reach_avatar_change_times_limit'));
        }

        // 依照用戶user_id組成, 上傳檔案的路徑
        $fullPathName = $this->getUserAvatarUploadFilePath($user_id);

        // 取得token
        $token = \CLStorage::getDriver()->uploadToken($fullPathName, 3600, ['insertOnly' => 0], true);
        //$disk = \CLStorage::disk('qiniu');
        //$token = $disk->getDriver()->uploadToken($fullPathName);

        $result = [
            'token' => $token,
            'file_path' => $fullPathName,
        ];
        return $result;
    }

    /**
     * 取得用戶頭像更新次數
     */
    public function getAvatarChangeTime($userId)
    {
        $userAvatarChangeTimesModel = $this->userAvatarChangeTimesRepository->findWhere([
            'user_id' => $userId,
        ]);
        $userAvatarChangeTimesCollection = $userAvatarChangeTimesModel->first();
        return $this->getAvatarCanChangeTimes($userAvatarChangeTimesCollection);
    }

    /**
     * 設定用戶的頭像url
     *
     * @param int $user_id
     * @param string $url
     */
    public function setAvatarUrl($user_id, $url)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($user_id);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }

        // 從db找出用戶的可更換頭像次數
        $userAvatarChangeTimesModel = $this->userAvatarChangeTimesRepository->findWhere([
            'user_id' => $user_id,
        ]);
        $userAvatarChangeTimesCollection = $userAvatarChangeTimesModel->first();
        // 檢查是否可以更新頭像
        if ($this->checkAvatarCanChange($userAvatarChangeTimesCollection) == false) {
            throw new \Exception(__('user.reach_avatar_change_times_limit'));
        }
        // 減少可更新頭像的次數
        $this->minusAvatarChangeTimes($user_id, $userAvatarChangeTimesCollection);

        // 更新頭像路徑資料
        $userModel->avatar = $url;
        $userModel->save();

        return $userModel->avatar;
    }

    /**
     * 上傳頭像(內部測試用)
     */
    public function uploadAvatar($id, $token, $file)
    {
        $fullPathName = $this->getUserAvatarUploadFilePath($id);
        //dd($fullPathName);
        $res = \CLStorage::upload($fullPathName, $file);
        //dd($res);
        dd(\CLStorage::url($fullPathName));
    }

    /**
     * 用戶變更密碼
     *
     * @param int $userId
     * @param string $newPassword
     */
    public function setPassword($userId, $newPassword)
    {
        // 使用user id來取出用戶驗證資料
        $userAuthCollection = $this->userAuthRepository->findWhere(['user_id' => $userId]);
        $userAuthModel = $userAuthCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userAuthModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }
        $userAuthModel->password = bcrypt($newPassword);
        $userAuthModel->save();
    }

    /**
     * 使用原有密碼來變更密碼
     *
     * @param int $userId
     * @param string $originPassword
     * @param string $newPassword
     */
    public function setPasswordByOrigin($userId, $originPassword, $newPassword)
    {
        // 使用user id來取出用戶驗證資料
        $userAuthCollection = $this->userAuthRepository->findWhere(['user_id' => $userId]);
        $userAuthModel = $userAuthCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userAuthModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }
        // 若舊密碼不合法, 回傳錯誤
        $valid = \Auth::guard('user_auth')->attempt(['user_id' => $userId, 'password' => $originPassword]);
        if (!$valid) {
            throw new \Exception(__('message.origin_password_invalid'));
        }

        //將密碼設定新密碼
        $userAuthModel->password = bcrypt($newPassword);
        $userAuthModel->save();
    }

    /**
     * 寫入用戶意見反饋資訊
     *
     * @param int $userId
     * @param string $typeSlug
     * @param string $contactInfo
     * @param string $feedback
     */
    public function setFeedback($userId, $typeSlug, $contactInfo, $feedback)
    {
        $data = [
            'user_id' => $userId,
            'type_slug' => $typeSlug,
            'contact_info' => $contactInfo,
            'feedback_info' => $feedback,
        ];
        $this->userFeedbackRepository->create($data);
    }

    /**
     * 用戶新增動態
     */
    public function postStory($userId, $photoUrl, $title)
    {
        $data = [
            'user_id' => $userId,
            'photo_url' => $photoUrl,
            'title' => $title,
        ];
        $this->userStoryWallRepository->create($data);
    }

    /**
     * 用戶移除動態
     */
    public function removeStory($userId, $storyId)
    {
        $this->userStoryWallRepository->deleteWhere([
            'user_id' => $userId,
            'id' => $storyId,
        ]);
    }

    /**
     * 用戶編輯動態
     */
    public function editStory($userId, $storyId, $photoUrl, $title)
    {
        $storyModel = $this->userStoryWallRepository->skipCache(true)->findWhere(
            [
                'user_id' => $userId,
                'id' => $storyId,
            ]
        )->first();
        if ($storyModel == null) {
            return;
        }
        $storyModel->title = $title;
        $storyModel->photo_url = $photoUrl;
        $storyModel->save();
    }

    /**
     * 取得動態列表
     */
    public function getStoryList($userId)
    {
        $modelArray = $this->userStoryWallRepository->skipCache(true)->findWhere(['user_id' => $userId])->sortByDesc('created_at')->all();
        $result = [];

        $length = count($modelArray);
        for ($i = 0; $i < $length; $i++) {
            $result[] = [
                'story_id' => $modelArray[$i]->id,
                'title' => $modelArray[$i]->title,
                'photo_url' => $modelArray[$i]->photo_url,
                'created_at' => $modelArray[$i]->created_at->format('Y-m-d h:i:s'),
                'fuzzy_date' => '',
            ];
        }
        return $result;
    }

    /**
     * 取得用戶動態圖檔上傳的token與路徑
     */
    public function getStoryPhotoTokenAndFilePath($userId)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($userId);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }

        // 依照用戶user_id組成, 上傳檔案的路徑
        $fullPathName = $this->getUserStoryPhotorUploadFilePath($userId);

        // 取得token
        $token = \CLStorage::getDriver()->uploadToken($fullPathName, 3600, ['insertOnly' => 0], true);

        $result = [
            'token' => $token,
            'file_path' => $fullPathName,
        ];
        return $result;
    }

    /**
     * 取得用戶動態主圖檔上傳的token與路徑
     */
    public function getStoryMainPhotoTokenAndFilePath($userId)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($userId);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $user_id]));
        }

        // 依照用戶user_id組成, 上傳檔案的路徑
        $fullPathName = $this->getUserStoryMainPhotorUploadFilePath($userId);

        // 取得token
        $token = \CLStorage::getDriver()->uploadToken($fullPathName, 3600, ['insertOnly' => 0], true);

        $result = [
            'token' => $token,
            'file_path' => $fullPathName,
        ];
        return $result;
    }

    /**
     * 設定用戶動態主圖url
     *
     * @param int $userId
     * @param string $url
     */
    public function setStoryMainPhotoUrl($userId, $url)
    {
        // 用user_id找出相對應的用戶
        $userCollection = $this->findById($userId);
        $userModel = $userCollection->first();

        // 若是用戶不存在,回傳錯誤
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }

        // 更新頭像路徑資料
        $userModel->story_main_photo = $url;
        $userModel->save();

        return $userModel->story_main_photo;
    }

    /**
     * 檢查是否可以更新頭像
     *
     * @param Collection $userAvatarChangeTimesCollection
     *
     * @return bool
     */
    protected function checkAvatarCanChange($userAvatarChangeTimesCollection)
    {
        if ($userAvatarChangeTimesCollection != null) {
            // 取得資料表上一次更新日期
            $lastUpdateDate = Carbon::parse($userAvatarChangeTimesCollection->updated_at);

            // 取得今日 日期
            $now = Carbon::now();

            // 若兩個日期同一天, 且可更新次數為 0, 不可更新頭像, 回傳false
            if ($now->isSameDay($lastUpdateDate) && $userAvatarChangeTimesCollection->number <= 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 取得用戶頭像可更新次數
     */
    protected function getAvatarCanChangeTimes($userAvatarChangeTimesCollection)
    {
        if ($userAvatarChangeTimesCollection == null) {
            return sc('avatar_change_times', UserAvatarChangeTimesModel::AVATAR_CHANGE_TIMES);
        }
        // 取得資料表上一次更新日期
        $lastUpdateDate = Carbon::parse($userAvatarChangeTimesCollection->updated_at);
        // 取得今日 日期
        $now = Carbon::now();

        // 若兩個日期不同一天, 回傳 AVATAR_CHANGE_TIMES
        if ($now->isSameDay($lastUpdateDate) == false) {
            return sc('avatar_change_times', UserAvatarChangeTimesModel::AVATAR_CHANGE_TIMES);
        } else {
            return $userAvatarChangeTimesCollection->number;
        }
    }

    /**
     * 將更新頭像次數-1
     *
     * @param int $user_id
     * @param Collection $userAvatarChangeTimesCollection
     *
     */
    protected function minusAvatarChangeTimes($user_id, $userAvatarChangeTimesCollection)
    {
        if ($userAvatarChangeTimesCollection != null) {
            // 將可更新次數-1, 並寫入db
            $userAvatarChangeTimesCollection->number = $userAvatarChangeTimesCollection->number - 1;
            $userAvatarChangeTimesCollection->save();
        } else {
            // 取得預設每日可更換頭像次數
            $times = sc('avatar_change_times', UserAvatarChangeTimesModel::AVATAR_CHANGE_TIMES);

            // 將可更新次數-1 (減去本次更換頭像的次數)
            $times = $times - 1;

            // 將資料寫入db
            $createData = [
                'user_id' => $user_id,
                'number' => $times,
            ];
            $this->userAvatarChangeTimesRepository->create($createData);
        }
    }

    /**
     * 取得頭像上傳到雲端的存放路徑
     *
     * @param int $user_id
     */
    protected function getUserAvatarUploadFilePath($user_id)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_AVATAR_FILENAME_PREFIX . $user_id . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_AVATAR_ROOT_DIRCTORY . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 取得主播封面圖上傳到雲端的存放路徑
     *
     * @param int $user_id
     */
    protected function getFrontcoverUploadFilePath($user_id)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_FRONTCOVER_FILENAME_PREFIX . $user_id . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_FRONTCOVER_ROOT_DIRCTORY . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 取得動態圖片上傳到雲端的存放路徑
     *
     * @param int $userId
     */
    protected function getUserStoryPhotorUploadFilePath($userId)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_STORY_PHOTO_FILENAME_PREFIX . $userId . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_STORY_PHOTO_ROOT_DIRCTORY . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 取得動態主圖片上傳到雲端的存放路徑
     *
     * @param int $userId
     */
    protected function getUserStoryMainPhotorUploadFilePath($userId)
    {
        $now = Carbon::now();
        // 依照用戶id組成, 上傳檔案的路徑
        $fileName = static::DEFAULT_STORY_MAIN_PHOTO_FILENAME_PREFIX . $userId . $now->timestamp;
        $fileName = base64_encode($fileName);

        $currnetDate = $now->format('Y-m-d');

        $fullPathName = static::DEFAULT_STORY_MAIN_PHOTO_ROOT_DIRCTORY . $currnetDate . '/' . $fileName;
        return $fullPathName;
    }

    /**
     * 取得用戶金幣
     */
    public function getUserGoldCacheById($id)
    {
        $userModel = $this->findById($id)->first();
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $id]));
        }
        return $userModel->gold_cache;
    }

    /**
     * 用戶基本資料
     * @param int $id
     *
     * @return array
     */
    public function info($id)
    {
        // 讀取快取資料
        $userInfo = $this->getUserDataFromCache($id);
        // 快取沒有資料, 從db讀取
        if ($userInfo == [] || $userInfo == null) {
            $userModel = UserModel::where(['id' => $id])->get()->first();
            if ($userModel == null) {
                throw new \Exception(__('user.not_found_user', ['user' => $id]));
            }
            // 組成API要使用的格式
            $userInfo = $this->getUserInfoApiDataFromCollection($userModel);

            // 將資料寫入cache
            $this->setUserDataToCache($id, $userInfo);
        }

        $userInfo['gold'] = $this->getUserGoldCacheById($id);

        // 設置用戶的封面圖
        $frontcover = $this->getFrontcoverByIds([$id]);
        if (isset($frontcover[$id])) {
            $userInfo['front_cover'] = $frontcover[$id];
        } else {
            $userInfo['front_cover'] = '';
        }

        // 若不是本人移除手機號
        $currentUserId = id();
        if ($userInfo['user_id'] != $currentUserId) {
            $userInfo['cellphone'] = '';
            $userInfo['is_follow'] = (int) ($this->followCheck($currentUserId, $userInfo['user_id']));
        } else {
            $userInfo['is_follow'] = 0;
        }

        $followAndFansNumber = $this->getFollowAndFansNumberFromCache($id);
        //取得是否isFollow
        $userInfo['follow_number'] = $followAndFansNumber['follow'];
        $userInfo['fans_number'] = $followAndFansNumber['fans'];
        return $userInfo;
    }

    /**
     * 取得 user info 的 cache key
     */
    public function getUserInfoCacheKey($userId)
    {
        $key = 'user_info' . ':' . $userId;
        return $key;
    }

    /**
     * 取得 anchor info 的 cache key
     */
    public function getAnchorInfoCacheKey($userId)
    {
        $key = 'anchor_info' . ':' . $userId;
        return $key;
    }

    /**
     * 取得 follow and fans number 的 cache key
     */
    public function getFollowAndFansNumberCacheKey($userId)
    {
        $key = 'follow_and_fans_number' . ':' . $userId;
        return $key;
    }

    /**
     * 從cache取得用戶的關注與追蹤數量
     */
    public function getFollowAndFansNumberFromCache($userId)
    {
        $key = $this->getFollowAndFansNumberCacheKey($userId);
        $result = \Cache::get($key);

        if ($result != null) {
            return $result;
        }
        return $this->updateFollowAndFansNumberCache($userId);
    }

    /**
     * 更新用戶的粉絲與關注數量
     */
    public function updateFollowAndFansNumberCache($userId)
    {

        $key = $this->getFollowAndFansNumberCacheKey($userId);

        // 取得所有粉絲
        $userFansCollection = $this->userFollowRepository->skipCache()->findByField('follow_uid', $userId);

        // 取得所有追蹤用戶
        $userFollowCollection = $this->userFollowRepository->skipCache()->findByField('user_id', $userId);

        $data = [
            'fans' => $userFansCollection->count(),
            'follow' => $userFollowCollection->count(),
            'follow_list' => $userFollowCollection->pluck('follow_uid')->toArray(),
        ];

        \Cache::forever($key, $data);
        return $data;
    }

    /**
     * 從cache取得所有用戶資料
     */
    public function getUserDataFromCache($userId)
    {
        $key = $this->getUserInfoCacheKey($userId);
        return \Cache::get($key);
    }

    /**
     * 設置用戶資料到cache中
     */
    public function setUserDataToCache($userId, $data)
    {
        $key = $this->getUserInfoCacheKey($userId);
        \Cache::forever($key, $data);
    }

    public function getUserInfoByIds($ids)
    {
        // 從cache中取得所有用戶基本資料
        if (!$ids) {
            return [];
        }
        $keys = [];
        for ($i = 0; $i < count($ids); $i++) {
            $key = $this->getUserInfoCacheKey($ids[$i]);
            $keys[] = $key;
        }
        if ($keys == []) {
            return [];
        }
        $allUserDataList = \Cache::many($keys);

        // 取得所有沒有cache資料的用戶id
        $noCacheIdList = [];
        foreach ($allUserDataList as $redisKey => $userData) {
            if ($allUserDataList[$redisKey] == null || $allUserDataList[$redisKey] == []) {
                list($prefix, $userId) = explode(":", $redisKey);
                $noCacheIdList[] = $userId;
            }
        }
        $allNoCacheUserInfo = [];
        if (count($noCacheIdList) > 0) {
            // 從db取得不在cache中的用戶基本資料
            $allInfoModel = $this->userRepository->findWhereIn('id', $noCacheIdList);
            $userInfoCollectionArray = $allInfoModel->all();
            for ($i = 0; $i < count($userInfoCollectionArray); $i++) {
                $info = $this->getUserInfoApiDataFromCollection($userInfoCollectionArray[$i]);
                $allUserDataList[] = $info;

                $key = $this->getUserInfoCacheKey($info['user_id']);
                $allNoCacheUserInfo[$key] = $info;
            }
        }
        // 將所有沒有cache的資料寫入cache
        if ($allNoCacheUserInfo != []) {
            \Cache::putMany($allNoCacheUserInfo, 60 * 24 * 365);
        }

        // 清掉空的cache資料
        $result = [];
        foreach ($allUserDataList as $userData) {
            if ($userData == null || $userData == []) {
                continue;
            }
            $result[] = $userData;
        }
        return $result;
    }

    /**
     * 用戶詳細訊息
     * @param int $id
     *
     * @return array
     */
    public function detailInfo($id)
    {
        $currentUserId = id();
        // 從快取中取得用戶資料
        $result = $this->getUserDataFromCache($id);

        if ($result == null || $result == []) {
            $userModel = UserModel::where(['id' => $id])->get()->first();
            if ($userModel == null) {
                throw new \Exception(__('user.not_found_user', ['user' => $id]));
            }

            // 組成API要使用的格式
            $result = $this->getUserInfoApiDataFromCollection($userModel);

            // 將資料寫入cache
            $this->setUserDataToCache($id, $result);
        }

        $result['gold'] = $this->getUserGoldCacheById($id);

        // 取得所有追蹤用戶的id號
        $userFollowModel = $this->userFollowRepository->skipCache(true)->findWhere(['user_id' => $id]);
        $userFollowCollectionArray = $userFollowModel->all();
        $allFollowIdList = [];
        for ($i = 0; $i < count($userFollowCollectionArray); $i++) {
            $userFollowArray = $userFollowCollectionArray[$i]->toArray();
            $allFollowIdList[] = $userFollowArray['follow_uid'];
        }

        // 取得所有粉絲的id號
        $userFansModel = $this->userFollowRepository->skipCache(true)->findWhere(['follow_uid' => $id]);
        $userFansCollectionArray = $userFansModel->all();
        $allFansIdList = [];
        for ($i = 0; $i < count($userFansCollectionArray); $i++) {
            $userFansArray = $userFansCollectionArray[$i]->toArray();
            $allFansIdList[] = $userFansArray['user_id'];
        }
        // 取得所有粉絲基本資料
        $allFansDataList = $this->getUserInfoByIds($allFansIdList);

        // 取得所有追蹤用戶基本資料
        $allFollowDataList = $this->getUserInfoByIds($allFollowIdList);

        // 取得所有用戶的封面圖
        $allIds = array_merge([$id], $allFansIdList, $allFollowIdList);
        $allFrontcover = $this->getFrontcoverByIds($allIds);

        // 設置用戶的封面圖
        if (isset($allFrontcover[$id])) {
            $result['frontcover'] = $allFrontcover[$id];
        } else {
            $result['frontcover'] = '';
        }

        // 設置所有關注用戶的封面圖路徑
        for ($i = 0; $i < count($allFollowDataList); $i++) {
            $userId = $allFollowDataList[$i]['user_id'];
            if (isset($allFrontcover[$userId])) {
                $allFollowDataList[$i]['frontcover'] = $allFrontcover[$userId];
            } else {
                $allFollowDataList[$i]['frontcover'] = '';
            }
            //把所有follow的用户加上is_follow ，有点脱裤子放屁的概念(都已经是关注了，还特别加一个is_follow)
            $allFollowDataList[$i]['is_follow'] = 1;
        }
        // 設置所有粉絲用戶的封面圖路徑
        for ($i = 0; $i < count($allFansDataList); $i++) {
            $userId = $allFansDataList[$i]['user_id'];
            if (isset($allFrontcover[$userId])) {
                $allFansDataList[$i]['frontcover'] = $allFrontcover[$userId];
            } else {
                $allFansDataList[$i]['frontcover'] = '';
            }

            //加上 is_follow
            $allFansDataList[$i]['is_follow'] = (int) ($this->followCheck($currentUserId, $userId));
        }

        // 移除關注用戶的手機號
        for ($i = 0; $i < count($allFollowDataList); $i++) {
            $allFollowDataList[$i]['cellphone'] = '';
        }
        // 移除粉絲用戶的手機號
        for ($i = 0; $i < count($allFansDataList); $i++) {
            $allFansDataList[$i]['cellphone'] = '';
        }

        // 若不是本人移除手機號
        if ($result['user_id'] != $currentUserId) {
            $result['cellphone'] = '';
        }

        // 配置統計資料到result
        $result['follow_number'] = count($userFollowModel);
        $result['fans_number'] = count($allFansIdList);

        // 排序: 主播->直播中->最近登入
        usort($allFollowDataList, function ($a, $b) {
            if ($b['is_anchor'] == $a['is_anchor']) {
                if ($b['is_live'] == $a['is_live']) {
                    return $b['last_login_at'] - $a['last_login_at'];
                }
                return $b['is_live'] - $a['is_live'];
            }
            return $b['is_anchor'] - $a['is_anchor'];
        });
        usort($allFansDataList, function ($a, $b) {
            if ($b['is_anchor'] == $a['is_anchor']) {
                if ($b['is_live'] == $a['is_live']) {
                    return $b['last_login_at'] - $a['last_login_at'];
                }
                return $b['is_live'] - $a['is_live'];
            }
            return $b['is_anchor'] - $a['is_anchor'];
        });

        $result['all_follow_data'] = $allFollowDataList;
        $result['all_fans_data'] = $allFansDataList;

        return $result;
    }

    /**
     * 依多個id取得封面圖
     *
     * @param array $ids
     *
     * @return array
     */
    public function getFrontcoverByIds($ids)
    {
        if (!$ids) {
            return [];
        }
        $anchorService = app(AnchorService::class);

        // 從快取讀取 anchor info 資料

        $keys = [];
        for ($i = 0; $i < count($ids); $i++) {
            $key = $this->getAnchorInfoCacheKey($ids[$i]);
            $keys[] = $key;
        }

        $allAnchorInfoList = \Cache::many($keys);

        $result = [];
        // 將快取資料整成 id => front cover 的格式
        foreach ($allAnchorInfoList as $redisKey => $value) {
            if ($allAnchorInfoList[$redisKey] == null || $allAnchorInfoList[$redisKey] == []) {
                continue;
            }
            $result[$allAnchorInfoList[$redisKey]['user_id']] = $allAnchorInfoList[$redisKey]['front_cover'];
        }

        // 所有不存在cache的id
        $allIdNotInCache = [];
        foreach ($allAnchorInfoList as $redisKey => $value) {
            if ($allAnchorInfoList[$redisKey] == null || $allAnchorInfoList[$redisKey] == []) {
                list($prefix, $userId) = explode(":", $redisKey);

                $allIdNotInCache[] = $userId;
            }
        }
        // 將db取出來的資料整合到result
        $allNoCacheAnchorInfo = [];
        if (count($allIdNotInCache) != 0) {
            $allAnchorInfoCollection = $this->anchorInfoRepository->findWhereIn('user_id', $ids);
            if ($allAnchorInfoCollection->count() != 0) {
                $allAnchorInfoModel = $allAnchorInfoCollection->all();
                for ($i = 0; $i < count($allAnchorInfoModel); $i++) {
                    $userId = $allAnchorInfoModel[$i]->user_id;
                    $result[$userId] = $allAnchorInfoModel[$i]->front_cover;

                    $key = $this->getAnchorInfoCacheKey($userId);
                    $allNoCacheAnchorInfo[$key] = $allAnchorInfoModel[$i];
                }
            }
        }

        // 將所有沒有cache的資料寫入cache
        if ($allNoCacheAnchorInfo != []) {
            \Cache::putMany($allNoCacheAnchorInfo, 60 * 24 * 365);
        }

        return $result;
    }

    /**
     * 使用id或暱稱搜尋用戶
     *
     * @param string $searchText
     *
     * @return array
     */
    public function searchByIdOrNickname($searchText): array
    {
        $userCollection = $this->userRepository->selectNickNameWithFullText($searchText);
        if ($userCollection->count() == 0) {
            return [];
        }

        $currentUserId = id();
        $result = [];
        for ($i = 0; $i < $userCollection->count(); $i++) {
            $userInfo = $this->getUserInfoApiDataFromCollection($userCollection[$i]);
            $userInfo['is_follow'] = (int) ($this->followCheck($currentUserId, $userInfo['user_id']));
            if ($userCollection[$i]->last_login_at == null) {
                $userInfo['last_login_at'] = 0;
            } else {
                $userInfo['last_login_at'] = Carbon::parse($userCollection[$i]->last_login_at)->timestamp;
            }

            array_push($result, $userInfo);
        }

        // 排序: 主播->等級->最近登入
        usort($result, function ($a, $b) {
            if ($b['is_anchor'] == $a['is_anchor']) {
                if ($b['level'] == $a['level']) {
                    return $b['last_login_at'] - $a['last_login_at'];
                }
                return $b['level'] - $a['level'];
            }
            return $b['is_anchor'] - $a['is_anchor'];
        });
        // 顯示前100名
        $result = array_slice($result, 0, 100);

        return $result;
    }

    /**
     * 追蹤一名用戶
     *
     * @param int $followingId
     * @param int $followedId
     */
    public function follow($followingId, $followedId)
    {
        if ($followingId == $followedId) {
            throw new \Exception(__('message.can_not_follow_self'));
        }

        // 檢查資料是否存在
        $userFollowCollection = $this->userFollowRepository->skipCache(true)->findWhere([
            'user_id' => $followingId,
            'follow_uid' => $followedId,
        ]);
        // 若是用戶已follow, 回傳錯誤
        if ($userFollowCollection->count() != 0) {
            throw new \Exception(__('message.user_already_follow'));
        }

        // 檢查輸入的followed id是否存在
        $followedUserCollection = $this->userRepository->findWhere(['id' => $followedId]);
        if ($followedUserCollection->count() == 0) {
            throw new \Exception(__('user.not_found_user', ['user' => $followedId]));
        }

        //將追蹤資料寫入DB
        $data = [
            'user_id' => $followingId,
            'follow_uid' => $followedId,
        ];
        $this->userFollowRepository->create($data);

        // 更新快取
        $this->updateFollowAndFansNumberCache($followingId);
        $this->updateFollowAndFansNumberCache($followedId);
    }

    /**
     * 取消追蹤一名用戶
     *
     * @param int $userId
     * @param int $cancelFollowId
     */
    public function followCancel($userId, $cancelFollowId)
    {
        if ($userId == $cancelFollowId) {
            throw new \Exception(__('message.can_not_follow_self'));
        }
        $where = [
            'user_id' => $userId,
            'follow_uid' => $cancelFollowId,
        ];
        $effectRow = $this->userFollowRepository->deleteWhere($where);
        if ($effectRow == 0) {
            return;
        }

        // 更新快取
        $this->updateFollowAndFansNumberCache($userId);
        $this->updateFollowAndFansNumberCache($cancelFollowId);
    }

    /**
     * 檢查指定用戶是否已被追蹤
     *
     * @param int $userId
     * @param int $targetUserId
     */
    public function followCheck($userId, $targetUserId)
    {
        if ($userId == $targetUserId) {
            return true;
        }
        //改用 快取
        $key = $this->getFollowAndFansNumberCacheKey($userId);
        $data = \Cache::get($key);
        if (is_null($data) || !isset($data['follow_list'])) {
            //重新从db取得
            $data = $this->updateFollowAndFansNumberCache($userId);
        }
        $followList = $data['follow_list'];
        return in_array($targetUserId, $followList);
        // $where = [
        //     'user_id' => $userId,
        //     'follow_uid' => $targetUserId,
        // ];

        // $collection = $this->userFollowRepository->findWhere($where);
        // if ($collection->count() == 0) {
        //     return false;
        // }
        // return true;
    }

    /**
     * 將某一位用戶加為聊天黑名單
     *
     * @param int $userId
     * @param int $blackUserId
     */
    public function addChatBlack($userId, $blackUserId)
    {
        if ($userId == $blackUserId) {
            throw new \Exception(__('message.can_not_black_self'));
        }

        // 檢查資料是否存在
        $userBlackCollection = $this->userChatBlackListRepository->skipCache(true)->findWhere([
            'user_id' => $userId,
            'black_user_id' => $blackUserId,
        ]);
        // 若是用戶已被黑名單, 回傳錯誤
        if ($userBlackCollection->count() != 0) {
            throw new \Exception(__('message.user_already_black'));
        }

        // 檢查輸入的id是否存在
        $blackUserCollection = $this->userRepository->findWhere(['id' => $blackUserId]);
        if ($blackUserCollection->count() == 0) {
            throw new \Exception(__('user.not_found_user', ['user' => $blackUserId]));
        }

        $data = [
            'user_id' => $userId,
            'black_user_id' => $blackUserId,
        ];
        $this->userChatBlackListRepository->create($data);
    }

    /**
     * 將某一位用戶解除聊天黑名單
     *
     * @param int $userId
     * @param int $blackUserId
     */
    public function removeChatBlack($userId, $blackUserId)
    {
        if ($userId == $blackUserId) {
            throw new \Exception(__('message.can_not_black_self'));
        }
        $where = [
            'user_id' => $userId,
            'black_user_id' => $blackUserId,
        ];
        $this->userChatBlackListRepository->deleteWhere($where);
    }

    /**
     * 聊天黑名單列表
     *
     * @param int $userId
     */
    public function chatBalckList($userId)
    {
        $blackCollection = $this->userChatBlackListRepository->skipCache(true)->findWhere(['user_id' => $userId]);
        $blackAllModel = $blackCollection->all();
        //dd($blackAllModel);
        $allBlackIdList = [];
        for ($i = 0; $i < count($blackAllModel); $i++) {
            $blackModel = $blackAllModel[$i];
            $allBlackIdList[] = $blackModel->black_user_id;
        }

        $allBlackInfoDataList = $this->getUserInfoByIds($allBlackIdList);

        $result = [
            'black_number' => count($allBlackInfoDataList),
            'all_black_user_info' => $allBlackInfoDataList,
        ];
        return $result;
    }

    /**
     * 增加開播時程表
     */
    public function addLiveSchedule($userId, $time)
    {
        $userModel = $this->userRepository->findWhere(
            ['id' => $userId]
        )->first();
        if ($userModel == null || $userModel->user_type_id != UserModel::USER_TYPE_ANCHOR) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }


        $time = Carbon::parse($time);
        $now = Carbon::now();

        if ($time <= $now) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }

        $allSchedule = $this->getLiveScheduleList($userId);

        // 判斷是否超過數量上限
        if (count($allSchedule) >= LiveScheduleModel::MAX_SCHEDULE_NUMBER) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }

        // 新增資料
        $newData = [
            'user_id' => $userId,
            'time' => $time,
        ];
        $this->liveScheduleRepository->create($newData);
    }

    /**
     * 移除開播時程表
     */
    public function removeLiveSchedule($userId, $scheduleId)
    {
        $userModel = $this->userRepository->findWhere(
            ['id' => $userId]
        )->first();
        if ($userModel == null || $userModel->user_type_id != UserModel::USER_TYPE_ANCHOR) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }

        $schedule = $this->liveScheduleRepository->findWhere(
            [
                'user_id' => $userId,
                'id' => $scheduleId,
            ]
        )->first();
        if ($schedule == null) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }
        $schedule->delete();
    }

    /**
     * 開播時程表
     */
    public function getLiveScheduleList($userId)
    {
        $userModel = $this->userRepository->findWhere(
            ['id' => $userId]
        )->first();
        if ($userModel == null || $userModel->user_type_id != UserModel::USER_TYPE_ANCHOR) {
            return [];
        }

        $now = Carbon::now();
        $allSchedule = $this->liveScheduleRepository->findWhere(['user_id' => $userId])->all();

        $length = count($allSchedule);
        // 過期的時程表
        $expiredSchedule = [];
        // 有效的時程表
        $validSchedule = [];
        for ($i=0; $i<$length; $i++) {
            $t = $allSchedule[$i]->time;
            if ($t <= $now) {
                $expiredSchedule[] = $allSchedule[$i];
            } else {
                $validSchedule[] = $allSchedule[$i];
            }
        }
        // 移除過期的schedule
        $length = count($expiredSchedule);
        for ($i=0; $i<$length; $i++) {
            $expiredSchedule[$i]->delete();
        }

        // 整理輸出格式
        $output = [];
        $length = count($validSchedule);
        for ($i=0; $i<$length; $i++) {
            $output[] = [
                'schedule_id' => $validSchedule[$i]->id,
                'schedule_time' => Carbon::parse($validSchedule[$i]->time)->format('Y-m-d H:i:s')
            ];
        }
        return $output;
    }

    /**
     * 從user的collection中, 組成api使用的資料格式
     *
     * @param Collection $userCollection
     *
     * @return array
     */
    public function getUserInfoApiDataFromCollection($userCollection)
    {
        // 判斷是否為主播
        $isAnchor = 0;
        if ($userCollection->user_type_id == UserModel::USER_TYPE_ANCHOR) {
            $isAnchor = 1;
        }
        // 修正sign簽名為空字串
        if ($userCollection->sign == null) {
            $sign = '';
        } else {
            $sign = $userCollection->sign;
        }

        // 修正intro為空字串
        if ($userCollection->intro == null) {
            $intro = '';
        } else {
            $intro = $userCollection->intro;
        }

        // 取得config設定
        $configModel = $this->userConfigRepository->skipCache(true)->findWhere(['user_id' => $userCollection->id])->first();

        // 判斷是否為代理
        $isAgent = 0;
        if ($configModel != null && $configModel->is_agent == LiveRoomModel::STATUS_LIVE) {
            $isAgent = 1;
        }

        // 是否通過實名認證
        if ($configModel) {
            $isVerifyRealName = $configModel->is_verify_real_name;
        } else {
            $isVerifyRealName = false;
        }

        // 判斷是否在開播
        $isLive = 0;
        if ($isAnchor) {
            $liveRoomModel = $this->liveRoomRepository->skipCache(true)->findWhere([
                'user_id' => $userCollection->id,
                'status' => LiveRoomModel::STATUS_LIVE]
            )->first();
            if ($liveRoomModel != null) {
                $isLive = 1;
            }
        }

        $lastLoginAt = 0;
        if ($userCollection->last_login_at != null) {
            $lastLoginAt = Carbon::parse($userCollection->last_login_at)->timestamp;
        }

        // 組成資料格式
        $userInfo = [
            'user_id' => $userCollection->id,
            'pretty_id' => $userCollection->pretty_id,
            'nick_name' => $userCollection->nickname,
            'cellphone' => $userCollection->cellphone,
            'birthday' => $userCollection->birthday,
            'avatar' => $userCollection->avatar,
            'story_main_photo' => $userCollection->story_main_photo,
            'sign' => $sign,
            'intro' => $intro,
            'sex' => $userCollection->sex,
            'level' => $userCollection->current_level,
            'exp' => $userCollection->current_exp,
            'is_anchor' => $isAnchor,
            'is_agent' => $isAgent,
            'is_live' => $isLive,
            'is_can_withdraw' => $userCollection->is_can_withdraw,
            'is_verify_real_name' => $isVerifyRealName,
            'gold' => $userCollection->gold,
            'last_login_at' => $lastLoginAt,
        ];
        return $userInfo;
    }

    public function all()
    {
        return $this->userRepository->paginate();
    }

    public function getUserRepository()
    {
        return $this->userRepository;
    }

    public function getUserAuthRepository()
    {
        return $this->userAuthRepository;
    }

    public function getUserFeedbackRepository()
    {
        return $this->userFeedbackRepository;
    }

    // public function getreal_name_verifyRepository()
    // {
    //     return $this->realNameVerifyRepository;
    // }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    /**
     * 用戶基本資料
     * @param int $id
     *
     * @return array
     */
    public function data($id)
    {
        $userModel = $this->findById($id);
        $userCollection = $userModel->first();
        if ($userCollection == null) {
            return response()->error();
        }
        return $userCollection->toArray();
    }
    /**
     * 创建主播
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-23T13:03:47+0800
     */
    public function newAnchorStore($data)
    {
        try {
            $nowYear = date("Y", time());
            //先开 user

            $userData = [
                'level' => getWhichExpByLevel($data['level']),
                'password' => bcrypt(UserAuth::passwordEncry($data['password'])),
                'user_type_id' => UserModel::USER_TYPE_ANCHOR,
                'cellphone' => $data['cellphone'],
                'nickname' => $data['real_name'],
                'sex' => UserModel::SEX_UNKNOW,
                'birthday' => ($nowYear - 16) . date("-m-d", time()),
                'avatar' => config('app.avatar'),
                'gold' => $data['gold'],
                'manager_id' => $data['manager'],
                'auth_type' => $this->userRepository::AUTH_PHONE,
            ];

            $user = $this->userRepository->create($userData);
            //写入实名认证
            //处理图片
            $ext = $data['photo']->getClientOriginalExtension();
            $photoPath = $this->realNameVerifyRepository->makeModel()::PHOTO_STORE_ROOT . '/' . date("Y-m-d");
            $photoPathName = date("Y-m-d") . '-' . uniqid() . '.' . $ext;
            $res = \CLStorage::upload($photoPath, $data['photo'], $photoPathName);
            $syncreal_name_verifyData = [
                'real_name' => $data['real_name'],
                'no' => $data['no'],
                'cellphone' => $data['cellphone'],
                'alipay_account' => $data['alipay_account'],
                'photo' => $res,
                'is_confirm' => $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_PASS,
            ];
            $realNameVerify = $user->real_name_verify()->create($syncreal_name_verifyData);
            $this->syncUserVerifyRealName($realNameVerify->id);
            $manager = $this->managerRepository->find($data['manager']);

            $syncAnchorData = [
                'user_id' => $user->id,
                'company_id' => $manager->company_id,
                'manager_id' => $data['manager'],
                'front_cover' => config('app.avatar'),
            ];

            $user->anchor()->create($syncAnchorData);
            return $user;
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    public function transferToAnchor($data)
    {
        try {
            $nowYear = date("Y", time());
            $userId = $data['user_id'];
            $user = $this->userRepository->with('real_name_verify')->findWhere(['id' => $userId]);
            if ($user->count() == 0) {
                throw new \Exception(__('user.not_found_user', ['user' => $userId]), 100);
            } else {
                $user = $user->first();
            }

            //检查实名认证
            if (!($user->real_name_verify && ($user->real_name_verify->is_confirm == $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_PASS))) {
                throw new \Exception(__('user.real_name_not_pass'), 100);
            }
            //写入实名认证
            $manager = $this->managerRepository->find($data['manager']);
            $syncAnchorData = [
                'user_id' => $user->id,
                'company_id' => $manager->company_id,
                'manager_id' => $data['manager'],
                'front_cover' => config('app.avatar'),
            ];
            $user->with('anchor');
            if ($user->anchor) {
                throw new \Exception(__('user.has_been_anchor'), 100);
            }
            $user->anchor()->create($syncAnchorData);
            $userData = [
                'user_type_id' => UserModel::USER_TYPE_ANCHOR,
                'manager_id' => $data['manager'],
            ];
            $user->update($userData);
            return $user;
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 檢查暱稱是否使用了系統預設的名稱
     *
     * @param int $id
     * @param int $name
     *
     * @return bool
     */
    protected function checkNicknameNotUseSystemName($id, $name)
    {
        if ($name == static::DEFAULT_USER_NICKNAME_PREFIX . $id) {
            return true;
        }
        //dd(substr($name, 0, strlen(static::DEFAULT_USER_NICKNAME_PREFIX)));
        if (static::DEFAULT_USER_NICKNAME_PREFIX == substr($name, 0, strlen(static::DEFAULT_USER_NICKNAME_PREFIX))) {
            return false;
        }
        return true;
    }

    public function saveRealNameVerify($id, $data)
    {
        $this->realNameVerifyRepository->update($data, $id);
        $this->syncUserVerifyRealName($id);
    }
    /**
     * 同步 User 的实名认证状态
     *
     * @param    [type]                   $id [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-24T17:05:18+0800
     */
    private function syncUserVerifyRealName($id)
    {
        $realNameVerify = $this->realNameVerifyRepository->with('user')->find($id);
        $realNameVerify->user->userConfig->is_verify_real_name = $realNameVerify->is_confirm;
        $realNameVerify->user->userConfig->save();
        if ($realNameVerify->is_confirm !== $realNameVerify::IS_CONFIRM_APPLYING) {
            $annouceData = [
                'user_id' => $realNameVerify->user->id,
                'type_slug' => AnnouceForUserModel::DEFAULT_TYPE_SLUG,
                'title' => ($realNameVerify->is_confirm == $realNameVerify::IS_CONFIRM_PASS) ? __('realNameVerify.pass') : __('realNameVerify.fail'),
                'content' => ($realNameVerify->is_confirm == $realNameVerify::IS_CONFIRM_PASS) ? __('realNameVerify.pass') : __('realNameVerify.fail'),
                'admin_id' => adminId(),
            ];
            $this->annouceForUserRepository->create($annouceData);

            // 用im傳送通知
            $broadCastData = [
                "SyncOtherMachine" => 2, // 消息不同步至发送方
                "To_Account" => (string) $realNameVerify->user->id,
                'MsgBody' => [
                    [
                        'MsgType' => 'TIMCustomElem',
                    ],
                ],
            ];
            $result = \IM::sendSingleUser($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.106', ['announceData' => 1])]]);
        }
    }

    /**
     * 新增代理
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-28T08:59:10+0800
     */
    public function insertAgent($data)
    {
        try {
            $nowYear = date("Y", time());
            //先开 user
            $userData = [
                'level' => getWhichExpByLevel($data['user']['currentLevel']),
                'password' => bcrypt(UserAuth::passwordEncry($data['userAuth']['password'])),
                'user_type_id' => UserModel::USER_TYPE_AGENT,
                'cellphone' => $data['real_name_verify']['cellphone'],
                'nickname' => $data['real_name_verify']['real_name'],
                'sex' => UserModel::SEX_UNKNOW,
                'birthday' => ($nowYear - 16) . date("-m-d", time()),
                'avatar' => config('app.avatar'),
                // 'gold' => $data['user']['gold'],
            ];
            $user = $this->userRepository->create($userData);

            //写入实名认证
            //处理图片
            if (isset($data['real_name_verify']['photo'])) {
                $ext = $data['real_name_verify']['photo']->getClientOriginalExtension();
                $photoPath = $this->realNameVerifyRepository->makeModel()::PHOTO_STORE_ROOT . '/' . date("Y-m-d");
                $photoPathName = date("Y-m-d") . '-' . uniqid() . '.' . $ext;
                $photoPathName = \CLStorage::upload($photoPath, $data['real_name_verify']['photo'], $photoPathName);
            } else {
                $photoPathName = "";
            }

            $syncreal_name_verifyData = [
                'real_name' => $data['real_name_verify']['real_name'],
                'no' => $data['real_name_verify']['no'],
                'cellphone' => $data['real_name_verify']['cellphone'],
                'alipay_account' => $data['real_name_verify']['alipay_account'],
                'photo' => $photoPathName,
            ];
            $user->real_name_verify()->create($syncreal_name_verifyData);
            $userConfigData = [
                'is_agent' => UserModel::IS_AGENT_YES,
                'assign_agent_date' => date("Y-m-d H:i:s", time()),
                'assign_agent_user_id' => adminId(),
            ];

            $user->userConfig()->update($userConfigData);
            $user = $this->userRepository->with('userConfig')->find($user->id);
            $this->userRepository->updateGold($user, $data['user']['gold'], $user->userConfig);
            return $user;
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 储存代理
     *
     * @param    integer                   $id   代理主键
     * @param    array                   $data 修改资料
     *
     * @return   User                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T14:16:07+0800
     */
    public function saveAgent($id, $data)
    {
        try {
            $nowYear = date("Y", time());
            $userConfig = $this->userConfigRepository->with('real_name_verify')->find($id);
            //先开 user
            $userData = [
                'level' => getWhichExpByLevel($data['user']['currentLevel']),
                // 'gold' => $data['user']['gold'],
            ];
            if ($data['userAuth']['password']) {
                $userData['password'] = bcrypt(UserAuth::passwordEncry($data['userAuth']['password']));
            }

            $user = $this->userRepository->update($userData, $data['user_id']);
            $this->userRepository->updateGold($user, $data['user']['gold'], $userConfig);
            //写入实名认证
            $syncreal_name_verifyData = [
                'real_name' => $data['real_name_verify']['real_name'],
                'no' => $data['real_name_verify']['no'],
                'cellphone' => $data['real_name_verify']['cellphone'],
                'alipay_account' => $data['real_name_verify']['alipay_account'],
            ];
            if (isset($data['real_name_verify']['photo'])) {
                $ext = $data['real_name_verify']['photo']->getClientOriginalExtension();
                $photoPath = $this->realNameVerifyRepository->makeModel()::PHOTO_STORE_ROOT;
                $photoPathName = uniqid() . '.' . $ext;
                $url = \CLStorage::upload($photoPath, $data['real_name_verify']['photo'], $photoPathName);
                $syncreal_name_verifyData['photo'] = $url;
                $originPhotoPath = $userConfig->real_name_verify->photo;
            }

            $userConfig->real_name_verify()->update($syncreal_name_verifyData);
            if (isset($data['real_name_verify']['photo'])) {
                \CLStorage::delete(decodeStoragePath($originPhotoPath));
            }
            return $user;
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 快速指定代理
     *
     * @param    array                   $data 资料
     *
     * @return   User                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T14:21:49+0800
     */
    public function quickAssignAgent($data)
    {
        try {
            $userConfig = $this->userConfigRepository->findWhere(['user_id' => $data['user_id']])->first();
            $userConfig->is_agent = UserModel::IS_AGENT_YES;
            $userConfig->assign_agent_date = date("Y-m-d H:i:s", time());
            $userConfig->assign_agent_user_id = adminId();
            $userConfig->save();
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 儲存封鎖狀態
     *
     * @param    int                   $id   流水號
     * @param    string                   $block_reason
     *
     * @return   void
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-08-28T16:14:18+0800
     */
    public function blockStore($id, $block_reason)
    {

        $userConfig = $this->userConfigRepository->find($id);
        $saveData = [
            'is_lock' => $userConfig::IS_BLOCK_YES,
            'block_reason' => $block_reason,
        ];
        $userConfig->update($saveData);
        $this->blockCallBack($userConfig);
    }

    /**
     * 这里统一处理，封号的相关行为
     *
     * @param    int                   $userId 主播id
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-03T16:57:01+0800
     */
    public function blockCallBack($userId)
    {
        //把开启的房间给关闭
        $liveRoomRepo = app(LiveRoomRepository::class);
        $rooms = $liveRoomRepo->findWhere(['user_id' => $userId, 'status' => $liveRoomRepo->makeModel()::STATUS_LIVE]);
        if ($rooms->count() > 0) {
            foreach ($rooms as $key => $room) {
                $room->status = $liveRoomRepo->makeModel()::STATUS_FORBIDDEN;
                $room->save();
            }
        }
    }
    /**
     * 初始化UserConfig
     *
     * @param    [type]                   $user [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-04T14:26:19+0800
     */
    public function initUserConfig($user)
    {
        $user->with('userConfig');
        if (!$user->userConfig) {
            $defaultData = [
                'is_lock' => $this->userConfigRepository->makeModel()::IS_BLOCK_NO,
            ];
            $user->userConfig()->create($defaultData);
        }
    }

    /**
     * 预设昵称
     *
     * @param    [type]                   $user [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-04T14:27:08+0800
     */
    public function defaultNickName($user)
    {
        $nickname = static::DEFAULT_USER_NICKNAME_PREFIX . strval($user->id);
        return $nickname;
    }

    /**
     * 透过userid , 建立一笔实名认证
     *
     * @param    int                   $userId 用户id
     * @param    array                   $data   认证资料
     *
     * @return   void
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T14:34:21+0800
     */
    public function insertRealNameVerify($userId, $data)
    {
        $nowYear = date("Y", time());
        $user = $this->userRepository->find($userId);
        $userConfig = $this->userConfigRepository->findWhere(['user_id' => $userId])->first();
        $data['is_confirm'] = $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_APPLYING;
        $this->insertRealNameVerifyOnUserConfig($userConfig, $data);
        return $user;
    }
    /**
     * 建立实名认识记录，透过UserConfig 跟 实名认识 的关连
     *
     * @param    UserConfig              $userConfig 用户其他设定资料
     * @param    array                   $data      认证资料
     *
     * @return   UserConfig              $userConfig 用户其他设定资料
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T14:35:44+0800
     */
    private function insertRealNameVerifyOnUserConfig(UserConfig $userConfig, $data)
    {
        //写入实名认证
        $syncreal_name_verifyData = [
            'real_name' => $data['real_name'],
            'no' => $data['no'],
            'cellphone' => $data['cellphone'],
            'alipay_account' => $data['alipay_account'],
            'is_confirm' => $data['is_confirm'] ?? $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_APPLYING,
        ];
        $userConfig->with('real_name_verify');
        $originPhotoPath = '';

        if ($userConfig->real_name_verify && $userConfig->real_name_verify->is_confirm == $this->realNameVerifyRepository->makeModel()::IS_CONFIRM_PASS) {
            throw new \Exception(__('realNameVerify.has_verify_error'));
        }
        if (isset($data['photo'])) {
            $ext = $data['photo']->getClientOriginalExtension();
            $photoPath = $this->realNameVerifyRepository->makeModel()::PHOTO_STORE_ROOT;
            $photoPathName = uniqid() . '.' . $ext;
            $url = \CLStorage::upload($photoPath, $data['photo'], $photoPathName);
            $syncreal_name_verifyData['photo'] = $url;
            if ($userConfig->real_name_verify) {
                $originPhotoPath = $userConfig->real_name_verify->photo;
            }
        }

        if ($userConfig->real_name_verify) {
            $userConfig->real_name_verify()->update($syncreal_name_verifyData);
            $this->syncUserVerifyRealName($userConfig->real_name_verify->id);
        } else {
            $realNameVerify = $userConfig->real_name_verify()->create($syncreal_name_verifyData);
            $this->syncUserVerifyRealName($realNameVerify->id);
        }

        if ($originPhotoPath) {
            \CLStorage::delete(decodeStoragePath($originPhotoPath));
        }
        return $userConfig;
    }

    /**
     * 取得所有代理 user_id
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T11:16:23+0800
     */
    public function allAgents()
    {
        return $this->userConfigRepository->with(['user'])->findWhere(['is_agent' => $this->userConfigRepository->makeModel()::IS_AGENT_YES], ['user_id']);
    }

    public function updateLoginByUser($user)
    {
        $now = date("Y-m-d H:i:s", time());
        $user->last_login_at = $now;
        $user->save();
    }

    /**
     * 建立測試假帳號
     *
     * @param    [type]                   $prefix [description]
     * @param    [type]                   $number [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-08T11:00:11+0800
     */
    public function userSeeder($prefix, $number)
    {
        \DB::table($this->userRepository->makeModel()->table)->truncate();
        \DB::table($this->userAuthRepository->makeModel()->table)->truncate();
        \DB::table($this->realNameVerifyRepository->makeModel()->table)->truncate();
        \DB::table($this->userConfigRepository->makeModel()->table)->truncate();
        $initId = 12000000;
        \DB::statement("ALTER TABLE user AUTO_INCREMENT = " . $initId . ";");
        $months = range(1, 12);
        $days = range(1, 28);
        $years = range(1990, 2000);
        $gold = 5000000 * sc('coinRatio');
        $level = getWhichExpByLevel(2);

        $realNameModel = $this->realNameVerifyRepository->makeModel();
        $realNameModel = $this->realNameVerifyRepository->makeModel();
        $userConfigModel = $this->userConfigRepository->makeModel();

        //一次写100笔记录
        $perRows = 10;
        $userId = $initId;
        for ($j = 0; $j < $number; $j += $perRows) {
            $end = $j + $perRows;
            $userDataList = [];
            $realNameDataList = [];
            $userConfigDataLisst = [];
            $userAuthDataList = [];
            for ($i = $j; $i < $end; $i++) {
                $birthday = $years[array_rand($years, 1)] . '-' . $months[array_rand($months, 1)] . '-' . $days[array_rand($days, 1)];
                $cellphone = 10000000000;
                $cellphone = $cellphone + $i;
                $real_name = '壓測主播' . $i;
                $userData = [
                    'level' => $level,
                    'user_type_id' => UserModel::USER_TYPE_ANCHOR,
                    'cellphone' => $cellphone,
                    'nickname' => $real_name,
                    'sex' => UserModel::SEX_FEMALE,
                    'birthday' => $birthday,
                    'avatar' => config('app.avatar'),
                    'gold' => $gold,
                    // 'auth_type' => $this->userRepository::AUTH_PHONE,
                ];
                $userDataList[] = $userData;
                $authData = [
                    'user_id' => $userId,
                    'password' => bcrypt(UserAuth::passwordEncry('12345678')),
                    'cellphone' => $cellphone,
                ];
                $userAuthDataList[] = $authData;
                $syncrealNameVerifyData = [
                    'user_id' => $userId,
                    'real_name' => $real_name,
                    'no' => $cellphone,
                    'cellphone' => $cellphone,
                    'alipay_account' => $cellphone,
                    'is_confirm' => $realNameModel::IS_CONFIRM_PASS,
                ];
                $realNameDataList[] = $syncrealNameVerifyData;
                //建立UserConfig
                $userConfigData = [
                    'is_lock' => $userConfigModel::IS_BLOCK_NO,
                    'user_id' => $userId,
                    'is_verify_real_name' => $realNameModel::IS_CONFIRM_PASS,
                ];
                $userConfigDataLisst[] = $userConfigData;
                $userId++;
            }

            $this->userRepository->makeModel()->insert($userDataList);
            app(UserAuth::class)->insert($userAuthDataList);
            $this->realNameVerifyRepository->makeModel()->insert($realNameDataList);
            $this->userConfigRepository->makeModel()->insert($userConfigDataLisst);
            echo '完成 ' . $j . '~' . $end . '主播建立' . PHP_EOL;
        }

    }
}
