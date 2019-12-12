<?php
namespace App\Services\IM;

/**
 * @SWG\Definition(
 *     definition="IM MsgType",
 *     description="讯类定义",
 *     @SWG\Property(
 *          property="101",
 *          type="integer",
 *          description="(傳送到個別直播間)当前直播间人数"
 *      ),
 *     @SWG\Property(
 *          property="102",
 *          type="string",
 *          description="(傳送到個別直播間)直播間送禮排行資訊"
 *      ),
 *     @SWG\Property(
 *          property="103",
 *          type="string",
 *          description="(傳送到個別直播間)直播間的熱度與金幣資訊-IM廣播"
 *      ),
 *      @SWG\Property(
 *          property="104",
 *          type="integer",
 *          description="(傳送到大直播間, group id=Common)被封设备号的用户ID, 收到这个讯息，前台需要强制做登出的动作"
 *      ),
 *     @SWG\Property(
 *          property="105",
 *          type="string",
 *          description="(傳送到個別直播間)熱門直播間排行榜-IM廣播"
 *      ),
 *     @SWG\Property(
 *          property="106",
 *          type="string",
 *          description="(傳送到個人用戶)個人系統公告, ex:1"
 *      ),
 *     @SWG\Property(
 *          property="107",
 *          type="string",
 *          description="(傳送到個別直播間)直播間的熱度資訊, ex:1"
 *      ),
 *     @SWG\Property(
 *          property="108",
 *          type="string",
 *          description="(傳送到大直播間, group id=Common)傳送門通知"
 *      )
 * )
 */
class IMManager
{
    //发送给前台APP 定义好的 Code
    const MESSAGE_TYPE_101 = 101; //(傳送到個別直播間)当前直播间人数
    const MESSAGE_TYPE_102 = 102; //(傳送到個別直播間)直播間送禮排行資訊
    const MESSAGE_TYPE_103 = 103; //(傳送到個別直播間)直播間的熱度與金幣資訊-IM廣播
    const MESSAGE_TYPE_104 = 104; //(傳送到大直播間, group id=Common)被封设备号的用户ID, 收到这个讯息，前台需要强制做登出的动作
    const MESSAGE_TYPE_105 = 105; //(傳送到個別直播間)熱門直播間排行榜
    const MESSAGE_TYPE_106 = 106; //(傳送到個人用戶)個人系統公告
    const MESSAGE_TYPE_107 = 107; //(傳送到個別直播間)直播間的熱度資訊-IM廣播
    const MESSAGE_TYPE_108 = 108; //(傳送到大直播間, group id=Common)傳送門通知

    protected $driver = '';
    protected $instances = [];
    public function __construct()
    {
        // $this->class = 'App\\Services\\IM\\' . ucfirst(config('im.driver')) . 'Instance';
        $this->driver = config('im.driver');
    }
    public function getInstance(string $instanceName)
    {
        if (!isset($this->instances[$instanceName])) {
            $this->instances[$instanceName] = app(config('im.instances.' . $instanceName . '.injection'));
        }
        return $this->instances[$instanceName];
    }

    public function instance( ? string $instance = null)
    {
        $instance = $instance ?: $this->driver;
        $this->instance = $this->getInstance($instance);
        return $this->instance;
    }

    public function resetInstance(string $instanceName = '')
    {
        $instance = $instanceName ?: $this->driver;
        unset($this->instances[$instance]);
        return $this->instance($instanceName);
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
        return $this->instance()->$method(...$parameters);
    }
}
