<?php
namespace App;

use Carbon\Carbon;

class JWTAuth
{
    protected static function publicKey()
    {
        return \File::get(app_path('Support/JWT/jwtRS256.key.pub'));
    }

    protected static function privateKey()
    {
        return \File::get(app_path('Support/JWT/jwtRS256.key'));
    }

    protected static function issue()
    {
        return env('JWT_ISSUE', config('app.url'));
    }

    public static function verify( ? string $token) : array
    {
        try {
            $decode = self::decode($token);

            $effectiveTime = Carbon::now()->between(Carbon::parse($decode->issue_at->date), Carbon::parse($decode->expired_at->date));

            $service = app('App\Services\UserService');
            $user = $service->getUserRepository()->find($decode->user_id);
            return $effectiveTime ? ['result' => true, 'message' => 'Verified', 'data' => $decode, 'user' => $user] : ['result' => false, 'message' => 'Token Expired'];
        } catch (\Exception $exception) {
            return ['result' => false, 'message' => 'Invalid Token', 'exception' => $exception->getMessage()];
        }
    }

    public static function decode($token)
    {
        $decode = \JWT::decode($token, self::publicKey(), ['RS256']);
        return $decode;
    }

    /**
     * 產生 JWT 資料
     *
     * @param array $auth
     * @return string
     */
    public static function generate($oldToken = '', $payload = []): string
    {
        if ($oldToken) {
            $decode = self::decode($oldToken);
            $userId = $decode->user_id;
        } else {
            $userId = \Auth::guard('user_auth')->user()->user_id;
        }
        $data = array(
            'uuid' => uniqid(),
            'issue' => self::issue(),
            'user_id' => $userId,
            'issue_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addDay(),
            'payload' => $payload,
        );
        $token = \JWT::encode($data, self::privateKey(), 'RS256');
        return $token;
    }

    /**
     * 這裡的登入可使用 (ID / cellphone + 密碼) 來進行登入
     *
     * @param array $auth
     * @return boolean
     */
    public static function valid(array $auth): bool
    {
        self::attemptUser($auth);
        return \Auth::guard('user_auth')->attempt(['cellphone' => $auth['cellphone'], 'password' => $auth['password']]) ||
        \Auth::guard('user_auth')->attempt(['user_id' => $auth['id'], 'password' => $auth['password']]);
    }

    /**
     * 這裡的登入可使用 (ID + 密碼) 來進行登入
     *
     * @param array $auth
     * @return boolean
     */
    public static function validID(array $auth): bool
    {
        self::attemptUserID($auth);
        return \Auth::guard('user_auth')->attempt(['user_id' => $auth['id'], 'password' => $auth['password']]);
    }

    /**
     * 貼加 user 資訊
     *
     * @param array $auth
     * @return void
     */
    private static function attemptUserID(array $auth): void
    {
        $userAuth = \App\Models\UserAuth::where(['user_id' => $auth['id'], 'password' => $auth['password']])->first();
        if (!is_null($userAuth)) {
            \Auth::loginUsingId($userAuth->user_id);
        }
    }

    /**
     * 貼加 user 資訊
     *
     * @param array $auth
     * @return void
     */
    private static function attemptUser(array $auth): void
    {
        $userAuth = \App\Models\UserAuth::where(['cellphone' => $auth['cellphone'], 'password' => $auth['password']])->orWhere(['user_id' => $auth['id'], 'password' => $auth['password']])->first();
        if (!is_null($userAuth)) {
            \Auth::loginUsingId($userAuth->user_id);
        }
    }
}
