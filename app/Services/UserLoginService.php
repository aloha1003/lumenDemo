<?php
namespace App\Services;

use App\Services\BlockDeviceService;
use App\Services\UserLoginRecordService;

//用户登入服务
class UserLoginService
{
    use \App\Traits\MagicGetTrait;
    private $blockDeviceRepository;
    private $userLoginRecordRepository;
    public function __construct(BlockDeviceService $blockDeviceService, UserLoginRecordService $userLoginRecordService)
    {
        $this->blockDeviceService = $blockDeviceService;
        $this->userLoginRecordService = $userLoginRecordService;
    }
    /**
     * 前台用户顺利登入之后，要写的相关表格记录
     *
     * @param    [type]                   $user [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-25T10:51:25+0800
     */
    public function pushUpdateRelatedRecordJob($user, $parameters)
    {
        \Queue::pushOn(config('user_login'), new \App\Jobs\UserLoginUpdateRelatedRecordJob($user, $parameters));

    }
    // 更新登入相关的表格
    public function updateRelatedRecordJob($user, $parameters)
    {
        $now = date("Y-m-d H:i:s", time());
        $user->last_login_at = $now;
        //更新连续登入日期
        $originLastLoginDate = $user->getOriginal()['last_login_at'] ?? 0;
        //比对上次时间是否有连续
        $originDate = date_create(date("Y-m-d", strtotime($originLastLoginDate)));
        $currentLoginDate = date_create(date("Y-m-d", strtotime($user->last_login_at)));
        $diff = date_diff($originDate, $currentLoginDate);
        $days = (int) $diff->format("%a");
        switch ($days) {
            case 0: //相同天数登入，不处理
                //Do nothing
                break;
            case 1: //差一天，表示是连续登入要+1
                $user->continue_login_days = $user->continue_login_days + 1;
                break;
            default: //其他天数，连续登入时间设为 1
                $user->continue_login_days = 1;
                break;
        }
        $user->save();
        $this->blockDeviceService->insert($parameters);
        $this->userLoginRecordService->insert($parameters);
    }
}
