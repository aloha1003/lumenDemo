<?php
namespace App\Services;

use App\Repositories\Interfaces\PayTypeIconRepository;

/**
 * 支付图示服务
 */
class PayTypeIconService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    const PAY_TYPE_ICONS_KEY = 'payTypeIconList';
    public function __construct(PayTypeIconRepository $repository)
    {
        $this->repository = $repository;
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
            $model = $this->repository->find($id);
            $originPhotoPath = $model->icon;
            $return = $model->update($data);
            if (isset($data['icon'])) {
                \CLStorage::delete(decodeStoragePath($originPhotoPath));
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
        if (isset($data['icon'])) {
            $photoPath = $this->repository->makeModel()::PHOTO_STORE_ROOT;
            $url = \CLStorage::upload($photoPath, $data['icon']);
            $data['icon'] = $url;
        }
        return $data;
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
        $photoPath = $this->repository->makeModel()::PHOTO_STORE_ROOT;
        $url = \CLStorage::upload($photoPath, $file);
        return $url;
    }
    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   PayTypeIcon               新增成功的PayTypeIcon
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        $data = $this->processCoverUpload($data);
        return $this->repository->create($data);
    }
    /**
     * 删除资料
     *
     * @param    [type]                   $id [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T13:15:14+0800
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * 更新图示
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T14:24:21+0800
     */
    public function refreshPayTypeIcon()
    {
        $all = $this->repository->all()->pluck('icon', 'slug')->toArray();
        \Cache::forever(self::PAY_TYPE_ICONS_KEY, $all);
        return $all;
    }

    /**
     * 透过slug取得图示
     *
     * @param    String                   $slug [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T14:24:56+0800
     */
    public function getIconBySlug($slug)
    {
        $all = \Cache::get(self::PAY_TYPE_ICONS_KEY);
        if (!isset($all[$slug])) {
            $all = $this->refreshPayTypeIcon();
            return $all[$slug] ?? __('payTypeIcon.unset_icon');
        } else {
            return $all[$slug];
        }
    }

    /**
     * 返回所有图示
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-16T14:36:19+0800
     */
    public function getAllIcon()
    {
        $all = \Cache::get(self::PAY_TYPE_ICONS_KEY);
        if (!$all) {
            $all = $this->refreshPayTypeIcon();
        }
        return $all;
    }
}
