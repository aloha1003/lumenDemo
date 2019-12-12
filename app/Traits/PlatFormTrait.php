<?php
namespace App\Traits;

/**
 * 关于多平台的显示
 */
trait PlatFormTrait
{
    /**
     * 取得平台对应的使用者
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-25T11:02:26+0800
     */
    public function getPlatformMapRelation($platform)
    {
        switch ($platform) {
            case self::FROM_WHICH_PLATFORM_ADMIN:
                return $this->belongsTo(config('admin.database.users_model'), 'source_user_id');
                break;
            case self::FROM_WHICH_PLATFORM_ANDROID:
            case self::FROM_WHICH_PLATFORM_API:
            case self::FROM_WHICH_PLATFORM_IOS:
                return $this->belongsTo('App\Models\User', 'source_user_id');
                break;
            case self::FROM_WHICH_PLATFORM_MANANGER:
                return $this->belongsTo('App\Models\Manager', 'source_user_id');
                break;
            case self::FROM_WHICH_PLATFORM_SYSTEM:
                return null;
                break;
            default:
                return $this->belongsTo('App\Models\User', 'source_user_id');
                break;
        }
    }

    /**
     * 取得对应的平台语系
     *
     * @param    [type]                   $attr [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-25T11:03:25+0800
     */
    public function platformLabel($attr)
    {
        if ($attr) {
            return __('common.platFormList.' . $attr);
        }
    }
}
