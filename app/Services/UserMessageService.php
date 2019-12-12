<?php
namespace App\Services;

use App\Repositories\Interfaces\UserMessageRepository;

//用户讯息服务
class UserMessageService
{
    use \App\Traits\MagicGetTrait;
    protected $repository;
    public function __construct(UserMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 新增Message
     *
     * @param    int                   $user_id 用户ID
     * @param    string                   $title   讯息标题
     * @param    string                   $content 讯息内容
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-17T15:29:37+0800
     */
    public function addMessage($user_id, $title, $content)
    {
        $status = $this->repository->makeModel()::STATUS_UNREAD;
        return $this->repository->create(compact('user_id', 'title', 'content', 'status'));
    }
}
