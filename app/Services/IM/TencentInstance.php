<?php

namespace App\Services\IM;

use App\Services\CurlApiService;

class TencentInstance implements IMInterface
{
    private $service;
    private $config;
    const LIVE_ROOM_GROUP_PREFIX = 'room';
    //API 回应成功的状态
    const API_REPONSE_OK = 'OK';
    public function __construct()
    {
        $this->config = $liveConfig = getTencentLiveConfigFromCache();

        $this->service = new CurlApiService($liveConfig);
    }

    public function initWithConfig()
    {
        $liveConfig = getTencentLiveConfigFromCache();
        config($config);
    }

    public function accountImport(array $request)
    {

    }
    /**
     * 建立群组
     *
     * @param    [type]                   $groupInfo [description]
     *
     * @return   [type]                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-16T13:49:04+0800
     */
    public function createGroup($groupInfo)
    {

        $this->service->setPath('v4/group_open_http_svc/create_group?' . $this->commonQueryString());
        $response = $this->service->post($groupInfo);
        return $response;
    }

    /**
     * 取得随机的32位无符号整数
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-16T13:52:52+0800
     */
    protected function rand()
    {
        return rand(1000000000, 2147483647);
    }

    public function getGroupList($requestParams = [])
    {

        $this->service->setPath('v4/group_open_http_svc/get_appid_group_list?' . $this->commonQueryString());
        if ($requestParams) {
            $response = $this->service->post($requestParams);
        } else {
            $response = $this->service->post();
        }
        return $response;
    }

    /**
     * 共同的查询
     *
     * @param    array                    $appendOption [description]
     *
     * @return   [type]                                 [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-16T13:59:51+0800
     */
    protected function commonQueryString($appendOption = [])
    {
        $props = [
            'sdkappid' => $this->config['system']['play_app_id'],
            'identifier' => $this->config['system']['identifier'],
            'usersig' => \Live::userSig($this->config['system']['identifier']),
            'random' => $this->rand(),
            'contenttype' => 'json',
        ];
        if ($appendOption) {
            $props = array_merge($props, $appendOption);
        }
        return http_build_query($props);
    }

    /**
     * 寄到大群
     */
    public function sendBroadCast($requestParams, $msgContentAry = [])
    {
        $requestParams['Random'] = $this->rand();
        return $this->sendWithURL('v4/group_open_http_svc/send_group_msg?', $requestParams, $msgContentAry);
    }

    /**
     * 寄到直播群
     */
    public function sendLiveRoomBroadcast($requestParams, $msgContentAry = [])
    {
        $requestParams['GroupId'] = self::getGroupId($requestParams['GroupId']);
        $requestParams['Random'] = $this->rand();
        return $this->sendWithURL('v4/group_open_http_svc/send_group_msg?', $requestParams, $msgContentAry);
    }

    /**
     * 寄給單一用戶
     */
    public function sendSingleUser($requestParams, $msgContentAry = [])
    {
        $requestParams['MsgRandom'] = $this->rand();
        return $this->sendWithURL('v4/openim/sendmsg?', $requestParams, $msgContentAry);
    }

    protected function sendWithURL($url, $requestParams, $msgContentAry = [])
    {

        if ($msgContentAry) {
            foreach ($msgContentAry as $key => $value) {
                if (isset($value['msg'])) {
                    $requestParams['MsgBody'][$key]['MsgContent'] = $value;
                    $requestParams['MsgBody'][$key]['MsgContent']['Data'] = $this->buildCustomMsg($value['msg']);
                }
                if (isset($value['desc'])) {
                    $requestParams['MsgBody'][$key]['MsgContent'] = $value['desc'];
                    $requestParams['MsgBody'][$key]['MsgContent']['Data'] = $this->buildCustomMsg($value['desc']);
                }
            }
        }
        $this->service->setPath($url . $this->commonQueryString());
        $response = $this->service->post($requestParams);
        return $response;
    }

    /**
     * 建立客制化讯息字串
     *
     * @param    [type]                   $locale [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-16T17:26:17+0800
     */
    protected function buildCustomMsg($locale)
    {
        $returnMessage = [
            'cmd' => 'CustomCmdMsg',

        ];
        $returnMessage['data'] = $locale;
        return json_encode($returnMessage);
    }
    /**
     * 返回群组的说明
     *
     * @param    array                    $requestParams [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T10:51:53+0800
     */
    public function getGroupInfoList($requestParams = [])
    {

        $this->service->setPath('v4/group_open_http_svc/get_group_info?' . $this->commonQueryString());
        if ($requestParams) {
            $response = $this->service->post($requestParams);
        } else {
            $response = $this->service->post();
        }
        return $response;
    }

    /**
     * 取得用户的在线状态
     *
     * @param    array                    $requestParams [description]
     *
     * @return   [type]                                  [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T10:53:31+0800
     */
    public function getUserOnlineState($requestParams = [])
    {
        $this->service->setPath('v4/openim/querystate?' . $this->commonQueryString());
        if ($requestParams) {
            $response = $this->service->post($requestParams);
        } else {
            $response = $this->service->post();
        }
        return $response;
    }

    /**
     * 取得当前在线人数
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T13:25:06+0800
     */
    public function getCurrentUserCount()
    {
        $totalCount = 0;
        try {
            $groupInfo = [
                'GroupIdList' => [
                    'Common',
                ],

                'ResponseFilter' => [
                    'GroupBaseInfoFilter' => [
                        'MemberNum',
                    ],
                    "MemberInfoFilter" => [ // 如果需要成员信息，请添加此数组
                        "Account", // 成员ID
                        "Role",
                    ],
                ],
            ];
            $result = $this->getGroupInfoList($groupInfo);
            if ($result['ActionStatus'] == self::API_REPONSE_OK) {
                $memerList = collect($result['GroupInfo'][0]['MemberList'])->map(function ($item) {
                    if ($item['Member_Account'] == '@TLS#NOT_FOUND') {
                        return [];
                    }
                    $item = $item['Member_Account'];
                    return $item;
                })->chunk(500)
                    ->toArray();

                foreach ($memerList as $key => $subList) {
                    $subList = array_filter($subList);
                    $subList = array_values(array_unique($subList));
                    $requestParams = ["To_Account" => $subList];
                    $onlineStatusListResult = $this->getUserOnlineState($requestParams);
                    if ($onlineStatusListResult['ActionStatus'] == self::API_REPONSE_OK) {
                        $onlineCount = collect($onlineStatusListResult['QueryResult'])->filter(function ($item) {
                            return $item['State'] == 'Online';
                        })->count();
                        $totalCount += $onlineCount;
                    }
                }
            }
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }

        return $totalCount;
    }

    /**
     * 取得IM 的群组 ID
     *
     * @param    [type]                   $groupId [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-08T14:21:18+0800
     */
    public static function getGroupId($groupId)
    {

        return static::LIVE_ROOM_GROUP_PREFIX . $groupId;
    }

    /**
     * 取得内部的UserId，透过 从im 的群组 ID
     *
     * @param    [type]                   $imGroupId [description]
     *
     * @return   [type]                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-08T14:22:54+0800
     */
    public static function getInnerGroupIdFromImGroupId($imGroupId)
    {
        return str_replace(static::LIVE_ROOM_GROUP_PREFIX, '', $imGroupId);
    }

}
