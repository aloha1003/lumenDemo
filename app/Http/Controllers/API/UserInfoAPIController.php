<?php
namespace App\Http\Controllers\API;

use App\Exceptions\Code;
use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\UserInfo\ForgetPasswordValidateRequestRule;
use App\Http\Requests\UserInfo\RealNameApplyRequest;
use App\Http\Requests\UserInfo\SmsSendForgetPasswordRequestRule;
use App\Http\Requests\UserInfo\UserBlackRequestRule;
use App\Http\Requests\UserInfo\UserChangePasswordSmsRequestRule;
use App\Http\Requests\UserInfo\UserEditBirthdayRequestRule;
use App\Http\Requests\UserInfo\UserEditNicknameRequestRule;
use App\Http\Requests\UserInfo\UserEditIntroRequestRule;
use App\Http\Requests\UserInfo\UserEditSexRequestRule;
use App\Http\Requests\UserInfo\UserEditSignRequestRule;
use App\Http\Requests\UserInfo\UserFollowCheckRequestRule;
use App\Http\Requests\UserInfo\UserFollowRequestRule;
use App\Http\Requests\UserInfo\UserInfoRequestRule;
use App\Http\Requests\UserInfo\UserSearchRequestRule;
use App\Http\Requests\UserInfo\UserSetAvatarUrlRequestRule;
use App\Http\Requests\UserInfo\UserSetFeedbackRequestRule;
use App\Http\Requests\UserInfo\UserSetFrontcoverUrlRequestRule;
use App\Http\Requests\UserInfo\UserSetPasswordBySmsRequestRule;
use App\Http\Requests\UserInfo\UserSetPasswordRequestRule;
use App\Http\Requests\UserInfo\UserStoryEditRequestRule;
use App\Http\Requests\UserInfo\UserStoryListRequestRule;
use App\Http\Requests\UserInfo\UserStoryPostRequestRule;
use App\Http\Requests\UserInfo\UserStoryRemoveRequestRule;
use App\Http\Requests\UserInfo\UserScheduleAddRequestRule;
use App\Http\Requests\UserInfo\UserScheduleRemoveRequestRule;
use App\Http\Requests\UserInfo\UserScheduleListRequestRule;
use App\Http\Requests\UserInfo\UserSetStoryMainPhotoUrlRequestRule;

use App\Services\SmsService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserInfoAPIController extends Controller
{
    private $userService;
    private $smsService;

    public function __construct(UserService $userService, SmsService $smsService)
    {
        $this->userService = $userService;
        $this->smsService = $smsService;
    }

    /**
     * @SWG\Post(
     *     path="/user/basic/info",
     *     tags={"用戶"},
     *     summary="用戶資料API",
     *     description="取得某一位用戶的資料",
     *     operationId="basicInfo",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要搜尋的用戶id, 若不帶, 則預設抓token登入的用戶資料",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="user_id", type="integer", example=12000001, description="真正的用户id"),
     *                  @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                  @SWG\Property(property="nick_name", type="string", example="大波露"),
     *                  @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                  @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                  @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="story_main_photo", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="sign", type="string", example="你好阿"),
     *                  @SWG\Property(property="info", type="string", example="這是我的簡介"),
     *                  @SWG\Property(property="sex", type="integer", example=0),
     *                  @SWG\Property(property="level", type="integer", example=0),
     *                  @SWG\Property(property="exp", type="integer", example=0),
     *                  @SWG\Property(property="is_anchor", type="integer", example=0),
     *                  @SWG\Property(property="is_agent", type="integer", example=0),
     *                  @SWG\Property(property="is_live", type="integer", example=0),
     *                  @SWG\Property(property="is_follow", type="integer", example=0, description="是否追踪(如果是本人就是0"),
     *                  @SWG\Property(property="gold", type="double", example=30.50),
     *                  @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                  @SWG\Property(property="is_verify_real_name", type="integer", example=0, description="0:尚未申请实名认证，1:通过，2:验证失败,3:等待审核中"),
     *                  @SWG\Property(property="frontcover", type="integer", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="follow_number", type="integer", example=1),
     *                  @SWG\Property(property="fans_number", type="integer", example=0)
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function basicInfo(UserInfoRequestRule $request)
    {
        $parameters = $request->all();
        $id = id();
        if (isset($parameters['target_user_id'])) {
            $id = $parameters['target_user_id'];
        }

        $userInfo = $this->userService->info($id);
        return response()->success($userInfo);
    }

    /**
     * @SWG\Post(
     *     path="/user/detail/info",
     *     tags={"用戶"},
     *     summary="用戶詳細資料API",
     *     description="取得某一位用戶的詳細資料",
     *     operationId="detailInfo",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要搜尋的用戶id, 若不帶, 則預設抓token登入的用戶資料",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="user_id", type="integer", example=12000000, description ="真正用戶的id"),
     *                  @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                  @SWG\Property(property="nick_name", type="string", example="大波露"),
     *                  @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                  @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                  @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="story_main_photo", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="sign", type="string", example="你好阿"),
     *                  @SWG\Property(property="info", type="string", example="這是我的簡介"),
     *                  @SWG\Property(property="sex", type="integer", example=0),
     *                  @SWG\Property(property="level", type="integer", example=0),
     *                  @SWG\Property(property="exp", type="integer", example=0),
     *                  @SWG\Property(property="is_anchor", type="integer", example=0),
     *                  @SWG\Property(property="is_agent", type="integer", example=0),
     *                  @SWG\Property(property="is_live", type="integer", example=0),
     *                  @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                  @SWG\Property(property="is_verify_real_name", type="integer", example=0),
     *                  @SWG\Property(property="gold", type="double", example=30.50),
     *                  @SWG\Property(property="follow_number", type="integer", example=13),
     *                  @SWG\Property(property="is_follow", type="integer", example=0, description="是否追踪(如果是本人就是0"),
     *                  @SWG\Property(property="fans_number", type="integer", example=22),
     *                  @SWG\Property(property="frontcover", type="integer", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(
     *                      property="all_follow_data",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="integer", example=12000002),
     *                          @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                          @SWG\Property(property="nick_name", type="string", example="大波露的追蹤者"),
     *                          @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                          @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                          @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                          @SWG\Property(property="story_main_photo", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                          @SWG\Property(property="sign", type="string", example="你好阿"),
     *                          @SWG\Property(property="info", type="string", example="這是我的簡介"),
     *                          @SWG\Property(property="sex", type="integer", example=0),
     *                          @SWG\Property(property="level", type="integer", example=0),
     *                          @SWG\Property(property="exp", type="integer", example=0),
     *                          @SWG\Property(property="is_anchor", type="integer", example=0),
     *                          @SWG\Property(property="is_agent", type="integer", example=0),
     *                          @SWG\Property(property="is_live", type="integer", example=0),
     *                          @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                          @SWG\Property(property="is_verify_real_name", type="integer", example=0, description="0:尚未申请实名认证，1:通过，2:验证失败,3:等待审核中"),
     *                          @SWG\Property(property="is_follow", type="integer", example=0, description="是否追踪(如果是本人就是0"),
     *                          @SWG\Property(property="gold", type="double", example=30.50),
     *                          @SWG\Property(property="frontcover", type="integer", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="all_fans_data",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="integer", example=12000001, description="真正的用户id"),
     *                          @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                          @SWG\Property(property="nick_name", type="string", example="大波露的粉絲"),
     *                          @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                          @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                          @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                          @SWG\Property(property="story_main_photo", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                          @SWG\Property(property="sign", type="string", example="你好阿"),
     *                          @SWG\Property(property="info", type="string", example="這是我的簡介"),
     *                          @SWG\Property(property="sex", type="integer", example=0),
     *                          @SWG\Property(property="level", type="integer", example=0),
     *                          @SWG\Property(property="exp", type="integer", example=0),
     *                          @SWG\Property(property="is_anchor", type="integer", example=0),
     *                          @SWG\Property(property="is_agent", type="integer", example=0),
     *                          @SWG\Property(property="is_live", type="integer", example=0),
     *                          @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                          @SWG\Property(property="is_verify_real_name", type="integer", example=0, description="0:尚未申请实名认证，1:通过，2:验证失败,3:等待审核中"),
     *                          @SWG\Property(property="is_follow", type="integer", example=0, description="是否追踪(如果是本人就是0"),
     *                          @SWG\Property(property="gold", type="double", example=30.50),
     *                          @SWG\Property(property="frontcover", type="integer", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                      )
     *                  ),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function detailInfo(UserInfoRequestRule $request)
    {
        $parameters = $request->all();
        $id = id();

        if (isset($parameters['target_user_id'])) {
            $id = $parameters['target_user_id'];
        }
        $detailInfo = $this->userService->detailInfo($id);
        return response()->success($detailInfo);
    }

    /**
     * @SWG\Post(
     *     path="/user/edit/nickname",
     *     tags={"用戶"},
     *     summary="用戶修改暱稱API",
     *     description="用戶修改暱稱, 暱稱不可與其他用戶重複, 且開頭不能使用系統預設 GSC_ ",
     *     operationId="editNickname",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="nickname",
     *         in="formData",
     *         description="用戶要修改的新暱稱",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editNickname(UserEditNicknameRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            \DB::beginTransaction();
            $this->userService->editNickname($id, $parameters['nickname']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/edit/sex",
     *     tags={"用戶"},
     *     summary="用戶修改性別API",
     *     description="修改用戶性別, 0:unknow, 1:male, 2:female",
     *     operationId="editSex",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sex",
     *         in="formData",
     *         description="用戶要修改的性別, 0:unknow, 1:male, 2:female",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editSex(UserEditSexRequestRule $request)
    {
        try {
            $parameters = $request->all();
            \DB::beginTransaction();
            $id = id();
            $this->userService->editSex($id, $parameters['sex']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/edit/birthday",
     *     tags={"用戶"},
     *     summary="用戶修改生日API",
     *     description="修改用戶生日, 格式為 YYY-MM-DD, 且日期不可超過今日",
     *     operationId="editBirthday",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="birthday",
     *         in="formData",
     *         description="用戶要修改的生日, 格式為 YYY-MM-DD, 且日期不可超過今日",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editBirthday(UserEditBirthdayRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            \DB::beginTransaction();
            $this->userService->editBirthday($id, $parameters['birthday']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/edit/sign",
     *     tags={"用戶"},
     *     summary="用戶修改簽名狀態API",
     *     description="修改用戶簽名狀態, 字數不得超過30字",
     *     operationId="editSign",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sign",
     *         in="formData",
     *         description="用戶要修改的簽名, 字數不得超過30字",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editSign(UserEditSignRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            \DB::beginTransaction();
            if (!isset($parameters['sign'])) {
                $parameters['sign'] = '';
            }
            $this->userService->editSign($id, $parameters['sign']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }


    /**
     * @SWG\Post(
     *     path="/user/edit/intro",
     *     tags={"用戶"},
     *     summary="用戶修改簡介API",
     *     description="用戶修改簡介",
     *     operationId="editIntro",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="intro",
     *         in="formData",
     *         description="用戶要修改的簡介",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editIntro(UserEditIntroRequestRule $request)
    {
        $parameters = $request->all();
        if (!isset($parameters['intro'])) {
            $parameters['intro'] = '';
        }
        $id = id();
        $this->userService->editIntro($id, $parameters['intro']);
        return response()->success(['status' => true]);
    }

    /**
     * @SWG\Post(
     *     path="/user/avatar/token",
     *     tags={"用戶"},
     *     summary="取頭像上傳的token",
     *     description="取頭像上傳的token, 若當日設置頭像超過3次, 會回傳錯誤",
     *     operationId="getAvatarToken",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="token", type="string", example="p-MTIxY_HqyS_f8_qBNK-m3FO7A8VCZwg8WaSWPs:i-w3bntp4LlxL5iLfO3HWDeTlx0=:eyJzY29wZSI6ImRkbGl2ZTphdmF0YXJcL1lYWmhkR0Z5TVRJd01EQXdNRE09IiwiZGVhZGxpbmUiOjE1NjczOTExODV9"),
     *                  @SWG\Property(property="file_path", type="string", example="avatar/YXZhdGFyMTIwMDAwMDM="),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getAvatarToken()
    {
        $id = id();
        $result = $this->userService->getAvatarTokenAndFilePath($id);
        return response()->success($result);
    }

    /**
     * @SWG\Post(
     *     path="/user/frontcover/token",
     *     tags={"用戶"},
     *     summary="取主播封面圖上傳的token",
     *     description="取主播封面圖上傳的token",
     *     operationId="getFrontcoverToken",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="token", type="string", example="p-MTIxY_HqyS_f8_qBNK-m3FO7A8VCZwg8WaSWPs:i-w3bntp4LlxL5i"),
     *                  @SWG\Property(property="file_path", type="string", example="avatar/YXZhdGFyMTIwMDAwMDM="),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getFrontcoverToken()
    {
        $id = id();
        $result = $this->userService->getFrontcoverTokenAndFilePath($id);
        return response()->success($result);

    }

    /**
     * @SWG\Post(
     *     path="/user/frontcover/url/set",
     *     tags={"用戶"},
     *     summary="設定主播封面圖片 API",
     *     description="設定主播封面圖片的url",
     *     operationId="setFrontcoverUrl",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="url",
     *         in="formData",
     *         description="主播封面圖片的url",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="url", type="string", example="http://ddlive.jusi888.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setFrontcoverUrl(UserSetFrontcoverUrlRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $id = id();
            $url = $this->userService->setFrontcoverUrl($id, $parameters['url']);
            \DB::commit();
            return response()->success(['url' => $url]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/avatar/url/set",
     *     tags={"用戶"},
     *     summary="設定用戶頭像 API",
     *     description="設定用戶頭像的url, 每日最多設定3次",
     *     operationId="setAvatarUrl",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="url",
     *         in="formData",
     *         description="用戶頭像的url",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="url", type="string", example="http://ddlive.jusi888.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setAvatarUrl(UserSetAvatarUrlRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $id = id();
            $url = $this->userService->setAvatarUrl($id, $parameters['url']);
            \DB::commit();
            return response()->success(['url' => $url]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/avatar/change/times",
     *     tags={"用戶"},
     *     summary="取得頭像可更新次數 API",
     *     description="取得頭像可更新次數",
     *     operationId="getAvatarChangeTimes",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="times", type="string", example=3),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getAvatarChangeTimes()
    {
        $id = id();
        $times = $this->userService->getAvatarChangeTime($id);
        return response()->success(['times' => $times]);
    }

    /**
     * 內部測試用的頭像上傳功能
     */
    public function avatarUpload(Request $request)
    {
        $id = id();
        $parameters = $request->all();

        $token = $parameters['token'];
        $file = $parameters['file'];

        $this->userService->uploadAvatar($id, $token, $file);

        return response()->success();
    }

    /**
     * @SWG\Post(
     *     path="/user/search",
     *     tags={"用戶"},
     *     summary="用戶搜尋API",
     *     description="搜尋其他用戶資料, 若找不到用戶 Response 的 data 欄位為 empty array",
     *     operationId="search",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="search_text",
     *         in="formData",
     *         description="要搜尋的用戶id或是暱稱",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="user_id", type="integer", example=12000000),
     *                      @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                      @SWG\Property(property="nick_name", type="string", example="大波露"),
     *                      @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                      @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                      @SWG\Property(property="avatar", type="string", example="http://www.cdn.com//image/avatar/default.jpg"),
     *                      @SWG\Property(property="story_main_photo", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                      @SWG\Property(property="sign", type="string", example="你好阿"),
     *                      @SWG\Property(property="info", type="string", example="這是我的簡介"),
     *                      @SWG\Property(property="sex", type="integer", example=0),
     *                      @SWG\Property(property="level", type="integer", example=0),
     *                      @SWG\Property(property="exp", type="integer", example=0),
     *                      @SWG\Property(property="is_anchor", type="integer", example=0),
     *                      @SWG\Property(property="is_agent", type="integer", example=0),
     *                      @SWG\Property(property="is_live", type="integer", example=0),
     *                      @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                      @SWG\Property(property="is_verify_real_name", type="integer", example=0, description="0:尚未申请实名认证，1:通过，2:验证失败,3:等待审核中"),
     *                      @SWG\Property(property="gold", type="double", example=30.50),
     *                      @SWG\Property(property="is_follow", type="integer", example=0),
     *                  )
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function search(UserSearchRequestRule $request)
    {
        $parameters = $request->all();
        $searchText = $parameters['search_text'];
        $result = $this->userService->searchByIdOrNickname($searchText);
        return response()->success($result);

    }

    /**
     * @SWG\Post(
     *     path="/user/follow",
     *     tags={"用戶"},
     *     summary="關注用戶",
     *     description="關注用戶",
     *     operationId="follow",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="follow_user_id",
     *         in="formData",
     *         description="要關注的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function follow(UserFollowRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $followingId = id();
            $followedId = $parameters['follow_user_id'];
            $this->userService->follow($followingId, $followedId);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/follow/cancel",
     *     tags={"用戶"},
     *     summary="取消關注用戶",
     *     description="取消關注用戶",
     *     operationId="followCancel",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="follow_user_id",
     *         in="formData",
     *         description="要取消關注的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function followCancel(UserFollowRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $followingId = id();

            $followedId = $parameters['follow_user_id'];
            $this->userService->followCancel($followingId, $followedId);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/follow/check",
     *     tags={"用戶"},
     *     summary="檢查用戶是否已追蹤API",
     *     description="檢查用戶是否已追蹤",
     *     operationId="followCheck",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要查詢的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="is_follow", type="bool", example=false),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function followCheck(UserFollowCheckRequestRule $request)
    {
        $parameters = $request->all();
        $userId = id();
        $result = $this->userService->followCheck($userId, $parameters['target_user_id']);
        return response()->success(['is_follow' => $result]);

    }

    /**
     * @SWG\Post(
     *     path="/user/password/sms/send",
     *     tags={"用戶"},
     *     summary="修改密碼的驗證碼API",
     *     description="傳送修改密碼的驗證碼到用戶的手機裡",
     *     operationId="sendChangePasswordSms",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="is_resend",
     *         in="formData",
     *         description="是否為重送, 0:否, 1:是, 預設為0",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function sendChangePasswordSms(UserChangePasswordSmsRequestRule $request)
    {
        // 產生驗證碼
        $code = $this->smsService->generateCode();
        $id = id();
        // 取得request的參數

        $resend = false;
        if (isset($parameters['is_resend']) && $parameters['is_resend'] == 1) {
            $resend = true;
        }

        // 送出簡訊
        $this->smsService->sendForChangePassword(
            trans('sms.password', ['validation_code' => $code]),
            $id,
            $code,
            $resend
        );

        // 返回成功結果
        return response()->success(['status' => true]);

    }

    /**
     * @SWG\Post(
     *     path="/forget/password/sms/send",
     *     tags={"用戶"},
     *     summary="忘記密碼的驗證碼API",
     *     description="傳送忘記密碼的驗證碼到用戶的手機裡",
     *     operationId="sendForgetPasswordSms",
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="用戶的手機, 11個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="is_resend",
     *         in="formData",
     *         description="是否為重送, 0:否, 1:是, 預設為0",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function sendForgetPasswordSms(SmsSendForgetPasswordRequestRule $request)
    {

        // 產生驗證碼
        $code = $this->smsService->generateCode();
        // 取得request的參數
        $parameters = $request->all();

        $resend = false;
        if (isset($parameters['is_resend']) && $parameters['is_resend'] == 1) {
            $resend = true;
        }

        // 送出簡訊
        $this->smsService->sendForForgetPassword(
            trans('sms.forget_password', ['validation_code' => $code]),
            $parameters['cellphone'],
            $code,
            $resend
        );

        // 返回成功結果
        return response()->success(['status' => true]);

    }

    /**
     * @SWG\Post(
     *     path="/forget/password/sms/validate",
     *     summary="驗證忘記密碼的簡訊驗證碼API",
     *     description="驗證忘記密碼的簡訊驗證碼",
     *     operationId="forgetPasswordSmsValidate",
     *     tags={"用戶"},
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="用戶的手機, 11個數字",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="sms_code",
     *         in="formData",
     *         description="簡訊驗證碼, 6個數字",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function forgetPasswordSmsValidate(ForgetPasswordValidateRequestRule $request)
    {

        // 取得request的參數
        $parameters = $request->all();
        if ($this->smsService->checkSmsCodeForForgetPassword($parameters['cellphone'], $parameters['sms_code']) == false) {
            throw new \Exception(__('message.sms_code_validation_fail'), Code::SMS_VALID_CODE_EXPIRED);
        }

        $newPassword = rand(10000000, 99999999);
        //$newPassword = 12345678;
        $this->smsService->sendForNewPassword(
            trans('sms.new_password', ['passowrd' => $newPassword]),
            $parameters['cellphone'],
            $newPassword
        );

        // 返回成功結果
        return response()->success(['status' => true]);

    }

    /**
     * @SWG\Post(
     *     path="/user/password/set/by/sms",
     *     tags={"用戶"},
     *     summary="使用sms簡訊驗證碼來修改用戶密碼",
     *     description="使用sms簡訊驗證碼來修改用戶密碼",
     *     operationId="setPasswordBySms",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="新的用戶密碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sms_code",
     *         in="formData",
     *         description="驗證碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setPasswordBySms(UserSetPasswordBySmsRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            $password = $parameters['password'];
            $smsCode = $parameters['sms_code'];

            \DB::beginTransaction();
            // 檢查驗證碼是否正確
            if ($this->smsService->checkSmsCodeForChangePassword($id, $smsCode) == false) {
                throw new \Exception(__('message.sms_code_validation_fail'), Code::SMS_VALID_CODE_EXPIRED);
            }
            // 設定新密碼
            $this->userService->setPassword($id, $password);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/password/set",
     *     tags={"用戶"},
     *     summary="使用原有密碼修改用戶密碼",
     *     description="使用原有密碼修改用戶密碼",
     *     operationId="setPassword",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="old_password",
     *         in="formData",
     *         description="舊的用戶密碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="new_password",
     *         in="formData",
     *         description="新的用戶密碼",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setPassword(UserSetPasswordRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            $oldPassword = $parameters['old_password'];
            $newPassword = $parameters['new_password'];
            \DB::beginTransaction();
            $this->userService->setPasswordByOrigin($id, $oldPassword, $newPassword);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/feedback/set",
     *     tags={"用戶"},
     *     summary="用戶意見反饋API",
     *     description="用戶意見反饋, 輸入文字中不能含有html標籤",
     *     operationId="setFeedback",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type_slug",
     *         in="formData",
     *         description="用戶意見反饋類型, 參考用戶意見反饋類型列表API( get: api/user/feedback/type/list)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="contact_info",
     *         in="formData",
     *         description="用戶聯絡資訊, 長度最多50",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="feedback_info",
     *         in="formData",
     *         description="用戶意見反饋資訊, 長度最多512",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setFeedback(UserSetFeedbackRequestRule $request)
    {
        try {
            $parameters = $request->all();
            $id = id();
            $typeSlug = $parameters['type_slug'];
            $contactInfo = $parameters['contact_info'];
            $feedbackInfo = $parameters['feedback_info'];
            \DB::beginTransaction();
            $this->userService->setFeedback($id, $typeSlug, $contactInfo, $feedbackInfo);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/real_name_apply",
     *     tags={"用戶"},
     *     summary="用户申请实名认证",
     *     description="用户申请实名认证, 輸入文字中不能含有html標籤",
     *     operationId="real_name_apply",
     *         consumes={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="real_name",
     *         in="formData",
     *         description="真实姓名",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="no",
     *         in="formData",
     *         description="身份证号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="cellphone",
     *         in="formData",
     *         description="手机号码",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="alipay_account",
     *         in="formData",
     *         description="支付宝帐号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="photo",
     *         in="formData",
     *         description="身份证照片",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function realNameApply(RealNameApplyRequest $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->only(['real_name', 'no', 'cellphone', 'alipay_account', 'photo']);
            $id = id();
            $this->userService->insertRealNameVerify($id, $parameters);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Get(
     *     path="/user/feedback/type/list",
     *     tags={"用戶"},
     *     summary="用戶意見反饋類型列表API",
     *     description="用戶意見反饋類型列表",
     *     operationId="feedbackTypeList",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="slug", type="string", example="purchase"),
     *                      @SWG\Property(property="name", type="string", example="购买与消费"),
     *                  )
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function feedbackTypeList()
    {
        $typeList = sc('feedbackType');
        $result = [];
        foreach ($typeList as $slug => $name) {
            $result[] = [
                'slug' => $slug,
                'name' => $name,
            ];
        }
        return response()->success($result);
    }

    /**
     * @SWG\Post(
     *     path="/user/add/black",
     *     tags={"用戶"},
     *     summary="加入黑名單API",
     *     description="用戶A將用戶B加入黑名單",
     *     operationId="addBlack",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="black_user_id",
     *         in="formData",
     *         description="要加入黑名單的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function addBlack(UserBlackRequestRule $request)
    {
        try {
            $id = id();
            $parameters = $request->all();
            \DB::beginTransaction();
            $this->userService->addChatBlack($id, $parameters['black_user_id']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/remove/black",
     *     tags={"用戶"},
     *     summary="解除黑名單API",
     *     description="用戶A將用戶B解除黑名單",
     *     operationId="removeBlack",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="black_user_id",
     *         in="formData",
     *         description="要解除黑名單的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function removeBlack(UserBlackRequestRule $request)
    {
        try {
            $id = id();
            $parameters = $request->all();
            \DB::beginTransaction();
            $this->userService->removeChatBlack($id, $parameters['black_user_id']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/black/list",
     *     tags={"用戶"},
     *     summary="用戶黑名單列表API",
     *     description="用戶黑名單列表",
     *     operationId="search",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="black_number",
     *                      type="integer",
     *                      format="int32"
     *                  ),
     *                  @SWG\Property(
     *                      property="all_black_user_info",
     *                      type="array",
     *                      format="int32",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="user_id", type="integer", example=12000000),
     *                          @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                          @SWG\Property(property="nick_name", type="string", example="大波露"),
     *                          @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                          @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                          @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                          @SWG\Property(property="sign", type="string", example="你好阿"),
     *                          @SWG\Property(property="sex", type="integer", example=0),
     *                          @SWG\Property(property="level", type="integer", example=0),
     *                          @SWG\Property(property="exp", type="integer", example=0),
     *                          @SWG\Property(property="is_anchor", type="integer", example=0),
     *                          @SWG\Property(property="is_agent", type="integer", example=0),
     *                          @SWG\Property(property="is_live", type="integer", example=0),
     *                          @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                          @SWG\Property(property="gold", type="double", example=30.50)
     *                      )
     *                  ),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function blackList()
    {
        $id = id();
        $result = $this->userService->chatBalckList($id);
        return response()->success($result);
    }

    /**
     * @SWG\Post(
     *     path="/user/story/list",
     *     tags={"用戶"},
     *     summary="用戶動態列表api",
     *     description="用戶動態列表api",
     *     operationId="getStoryList",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="要搜尋的用戶id, 若不帶, 則預設抓token登入的用戶資料",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="story_id", type="string", example=12300, description="動態id"),
     *                  @SWG\Property(property="title", type="string", example="這是劉德華發的第一個動態", description="動態標題"),
     *                  @SWG\Property(property="photo_url", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                  @SWG\Property(property="created_at", type="string", example="2019-10-10 11:03:35"),
     *                  @SWG\Property(property="fuzzy_date", type="string", example="10分鐘前")
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getStoryList(UserStoryListRequestRule $request)
    {
        $parameters = $request->all();
        $userId = id();
        if (isset($parameters['target_user_id'])) {
            $userId = $parameters['target_user_id'];
        }
        $result = $this->userService->getStoryList($userId);

        return response()->success($result);
    }

    /**
     * @SWG\Post(
     *     path="/user/story/post",
     *     tags={"用戶"},
     *     summary="用戶新增動態api",
     *     description="用戶新增動態api",
     *     operationId="postStory",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         description="動態標題",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="photo_url",
     *         in="formData",
     *         description="圖檔路徑",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function postStory(UserStoryPostRequestRule $request)
    {
        $userId = id();
        $parameters = $request->all();
        $photoUrl = $parameters['photo_url'];
        $title = $parameters['title'];
        $this->userService->postStory($userId, $photoUrl, $title);

        return response()->success(['status' => true]);
    }

    /**
     * @SWG\Post(
     *     path="/user/story/remove",
     *     tags={"用戶"},
     *     summary="用戶刪除動態api",
     *     description="用戶刪除動態api",
     *     operationId="removeStory",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="story_id",
     *         in="formData",
     *         description="動態id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function removeStory(UserStoryRemoveRequestRule $request)
    {
        $userId = id();

        $parameters = $request->all();

        $storyId = $parameters['story_id'];
        $this->userService->removeStory($userId, $storyId);

        return response()->success(['status' => true]);
    }

    /**
     * @SWG\Post(
     *     path="/user/story/edit",
     *     tags={"用戶"},
     *     summary="用戶編輯動態api",
     *     description="用戶編輯動態api",
     *     operationId="editStory",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="story_id",
     *         in="formData",
     *         description="動態id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         description="動態標題",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="photo_url",
     *         in="formData",
     *         description="圖檔路徑",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function editStory(UserStoryEditRequestRule $request)
    {
        $userId = id();

        $parameters = $request->all();

        $storyId = $parameters['story_id'];
        $photoUrl = $parameters['photo_url'];
        $title = $parameters['title'];

        $this->userService->editStory($userId, $storyId, $photoUrl, $title);

        return response()->success(['status' => true]);
    }

    /**
     * @SWG\Post(
     *     path="/user/story/photo/token",
     *     tags={"用戶"},
     *     summary="動態圖檔上傳的token",
     *     description="取動態圖檔上傳的token",
     *     operationId="getStoryPhotoToken",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="token", type="string", example="p-MTIxY_HqyS_f8_qBNK-m3FO7A8VCZwg8WaSWPs:i-w3bntp4LlxL5iLfO3HWDeTlx0=:eyJzY29wZSI6ImRkbGl2ZTphdmF0YXJcL1lYWmhkR0Z5TVRJd01EQXdNRE09IiwiZGVhZGxpbmUiOjE1NjczOTExODV9"),
     *                  @SWG\Property(property="file_path", type="string", example="avatar/YXZhdGFyMTIwMDAwMDM="),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getStoryPhotoToken()
    {
        $userId = id();

        $result = $this->userService->getStoryPhotoTokenAndFilePath($userId);

        return response()->success($result);
    }

    /**
     * @SWG\Post(
     *     path="/user/schedule/list",
     *     tags={"用戶"},
     *     summary="用戶開播預告列表",
     *     description="用戶開播預告列表s",
     *     operationId="getLiveSchedule",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="target_user_id",
     *         in="formData",
     *         description="目標用戶id, 沒有帶會使用token的用戶",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="schedule_id", type="string", example=3),
     *                      @SWG\Property(property="schedule_time", type="string", example="2019-12-08 00:05:00"),
     *                  )
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getLiveSchedule(UserScheduleListRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            
            $parameters = $request->all();
            if (isset($parameters['target_user_id']) == false) {
                $parameters['target_user_id'] = id();
            }
            $result = $this->userService->getLiveScheduleList($parameters['target_user_id']);

            \DB::commit();

            return response()->success($result);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

    /**
     * @SWG\Post(
     *     path="/user/schedule/add",
     *     tags={"用戶"},
     *     summary="用戶新增開播預告",
     *     description="用戶新增開播預告",
     *     operationId="addLiveSchedule",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="time",
     *         in="formData",
     *         description="時程, (格式: 2019-01-01 00:00:00)",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function addLiveSchedule(UserScheduleAddRequestRule $request)
    {
        try {
            \DB::beginTransaction();

            $userId = id();
            $parameters = $request->all();
            $this->userService->addLiveSchedule($userId, $parameters['time']);
    
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }


    /**
     * @SWG\Post(
     *     path="/user/schedule/remove",
     *     tags={"用戶"},
     *     summary="用戶移除開播預告",
     *     description="用戶移除開播預告",
     *     operationId="removeLiveSchedule",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="schedule_id",
     *         in="formData",
     *         description="開播預告id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(ref="#/definitions/StatusResponseModel")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function removeLiveSchedule(UserScheduleRemoveRequestRule $request)
    {
        $userId = id();
        $parameters = $request->all();

        $this->userService->removeLiveSchedule($userId, $parameters['schedule_id']);

        return response()->success(['status' => true]);
    }


    /**
     * @SWG\Post(
     *     path="/user/story/main_photo/token",
     *     tags={"用戶"},
     *     summary="取動態主圖上傳的token",
     *     description="取動態主圖上傳的token",
     *     operationId="getStoryMainPhotoToken",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="token", type="string", example="p-MTIxY_HqyS_f8_qBNK-m3FO7A8VCZwg8WaSWPs:i-w3bntp4LlxL5iLfO3HWDeTlx0=:eyJzY29wZSI6ImRkbGl2ZTphdmF0YXJcL1lYWmhkR0Z5TVRJd01EQXdNRE09IiwiZGVhZGxpbmUiOjE1NjczOTExODV9"),
     *                  @SWG\Property(property="file_path", type="string", example="avatar/YXZhdGFyMTIwMDAwMDM="),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getStoryMainPhotoToken()
    {
        $userId = id();
        $result = $this->userService->getStoryMainPhotoTokenAndFilePath($userId);
        return response()->success($result);
    }


    /**
     * @SWG\Post(
     *     path="/user/story/main_photo/url/set",
     *     tags={"用戶"},
     *     summary="設定用戶動態主圖url API",
     *     description="設定用戶動態主圖url",
     *     operationId="setStoryMainPhotoUrl",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="url",
     *         in="formData",
     *         description="用戶動態主圖url",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="請求成功",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="url", type="string", example="http://ddlive.jusi888.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function setStoryMainPhotoUrl(UserSetStoryMainPhotoUrlRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $parameters = $request->all();
            $userId = id();
            $url = $this->userService->setStoryMainPhotoUrl($userId, $parameters['url']);
            \DB::commit();
            return response()->success(['url' => $url]);
        } catch (\Exception $ex) {
            \DB::rollback();
            throw $ex;
        }
    }

}
