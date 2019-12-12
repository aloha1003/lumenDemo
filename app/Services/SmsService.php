<?php

namespace App\Services;

use App\Repositories\Interfaces\UserAuthRepository;
use App\Repositories\Interfaces\UserRepository;

//简讯服务
class SmsService
{
    private $userRepository;
    private $userAuthRepository;

    const CACHE_KEY_REGISTER_PREFIX = 'register';
    const CACHE_KEY_CHANGE_PASSWORD_PREFIX = 'password';
    const CACHE_KEY_FORGET_PASSWORD_PREFIX = 'forget_password';
    const CACHE_KEY_FORGET_SEND_NEW_PASSWORD_PREFIX = 'forget_send_new_password';
    const CACHE_KEY_NEW_PASSWORD_PREFIX = 'new_password';
    const COOL_DOWN_TIME = 10;
    public function __construct(
        UserRepository $userRepository,
        UserAuthRepository $userAuthRepository) {
        $this->userRepository = $userRepository;
        $this->userAuthRepository = $userAuthRepository;
    }

    /**
     * 產生驗證碼
     *
     * @return integer
     */
    public function generateCode(): string
    {
        $r = (string) rand(100000, 999999);
        return $r;
    }

    /**
     * 送出註冊用的簡訊，並寫入資料庫
     *
     * @param array $parameters
     * @param string $message
     * @return void
     */
    public function send(array $parameters, string $message, $force = false)
    {
        // 發送簡訊驗證碼
        if (config('sms.real_send') == 0) {
            return true;
        }
        // 創建驗證碼並保存到數據庫
        $key = self::CACHE_KEY_REGISTER_PREFIX . ':' . $parameters['cellphone'];

        if ($force == false) {
            $hasSend = \Cache::get($key);
            if ($hasSend) {
                return true;
            }
        }

        $response = \Sms::setMessageType(\Sms::instance()::MESSAGE_TYPE_REGISTER_VERIFY)->send([
            'mobile' => $parameters['cellphone'],
            'nationcode' => $parameters['nationcode'],
            'message' => $message,
        ]);
        // 快取 10分钟
        \Cache::put($key, $message, self::COOL_DOWN_TIME);
    }

    /**
     * 送出修改密碼的簡訊，並寫入資料庫
     *
     * @param string $message
     * @param int $user_id
     * @param int $code
     *
     * @return void
     */
    public function sendForChangePassword($message, $userId, $code, $force = false)
    {
        // 發送簡訊驗證碼
        if (config('sms.real_send') == 0) {
            return true;
        }

        // 用id找出相對應的用戶
        $userModel = $this->userRepository->findWhere(['id' => $userId]);
        $userCollection = $userModel->first();

        // 若是用戶不存在,回傳錯誤
        if ($userCollection == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }
        $cellphone = $userCollection->cellphone;
        // 創建驗證碼並保存到數據庫
        $key = self::CACHE_KEY_CHANGE_PASSWORD_PREFIX . ':' . $cellphone;
        if ($force == false) {
            $hasSend = \Cache::get($key);
            if ($hasSend) {
                return true;
            }
        }
        $respons = \Sms::setMessageType(\Sms::instance()::MESSAGE_TYPE_REGISTER_VERIFY)->send([
            'mobile' => $cellphone,
            'message' => $code,
        ]);
        \Cache::put($key, $code, self::COOL_DOWN_TIME);
    }

    /**
     * 發送忘記密碼簡訊驗證碼
     */
    public function sendForForgetPassword($message, $cellphone, $code, $force = false)
    {

        $authModel = $this->userAuthRepository->findWhere(['cellphone' => $cellphone])->first();
        // 若是手機號碼不存在,回傳錯誤
        if ($authModel == null) {
            throw new \Exception(__('user.cellphone_not_exist'));
        }
        if (config('sms.real_send') == 0) {
            return true;
        }

        // 創建驗證碼並保存到數據庫
        $key = self::CACHE_KEY_FORGET_PASSWORD_PREFIX . ':' . $cellphone;
        if ($force == false) {
            $hasSend = \Cache::get($key);
            if ($hasSend) {
                return true;
            }
        }
        $response = \Sms::setMessageType(\Sms::instance()::MESSAGE_TYPE_REGISTER_VERIFY)->send([
            'mobile' => $cellphone,
            'message' => $code,
        ]);

        \Cache::put($key, $code, self::COOL_DOWN_TIME);
    }

    /**
     * 傳送新密碼的簡訊
     */
    public function sendForNewPassword($message, $cellphone, $password)
    {
        // 創建驗證碼並保存到數據庫
        $key = self::CACHE_KEY_FORGET_SEND_NEW_PASSWORD_PREFIX . ':' . $cellphone;
        $hasSend = \Cache::get($key);
        if ($hasSend) {
            // return true;
        }
        $authModel = $this->userAuthRepository->findWhere(['cellphone' => $cellphone])->first();
        // 若是手機號碼不存在,回傳錯誤
        if ($authModel == null) {
            throw new \Exception(__('user.cellphone_not_exist'));
        }
        if (config('sms.real_send') == 0) {
            return true;
        }

        $rawPassword = (string) $password;
        $password = $this->userAuthRepository->model()::passwordEncry($password);
        $authModel->password = bcrypt($password);
        $authModel->save();
        $response = \Sms::setMessageType(\Sms::instance()::MESSAGE_TYPE_RESENT_PASSWORD)->send([
            'mobile' => $cellphone,
            'message' => $rawPassword,
        ]);
        \Cache::put($key, $rawPassword, self::COOL_DOWN_TIME);
    }

    /**
     * 檢查sms code是否正確
     *
     * @param string $cellphone
     * @param string $smsCode
     *
     * @return bool
     */
    public function checkSmsCode(string $cellphone, string $smsCode): bool
    {
        if (config('sms.real_send') == 0) {
            return true;
        }
        $key = self::CACHE_KEY_REGISTER_PREFIX . ':' . $cellphone;
        $code = \Cache::get($key);
        return ($code == $smsCode);
    }

    /**
     * 檢查更換密碼的sms code是否正確
     *
     * @param string $userId
     * @param string $smsCode
     */
    public function checkSmsCodeForChangePassword(string $userId, string $smsCode)
    {
        if (config('sms.real_send') == 0) {
            return true;
        }
        // 用id找出相對應的用戶
        $userModel = $this->userRepository->findWhere(['id' => $userId]);
        $userCollection = $userModel->first();

        // 若是用戶不存在,回傳錯誤
        if ($userCollection == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }
        $cellphone = $userCollection->cellphone;

        $key = self::CACHE_KEY_CHANGE_PASSWORD_PREFIX . ':' . $cellphone;
        $code = \Cache::get($key);
        return ($code == $smsCode);
    }

    /**
     * 檢查忘記密碼的sms code是否正確
     *
     * @param string $cellphone
     * @param string $smsCode
     */
    public function checkSmsCodeForForgetPassword(string $cellphone, string $smsCode)
    {
        if (config('sms.real_send') == 0) {
            return true;
        }
        $key = self::CACHE_KEY_FORGET_PASSWORD_PREFIX . ':' . $cellphone;
        $code = \Cache::get($key);
        return ($code == $smsCode);
    }

}
