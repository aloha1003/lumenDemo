<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Code;
use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Requests\Agent\AgentAddUserToNameListRequestRule;
use App\Http\Requests\Agent\AgentSetUserToStarRequestRule;
use App\Http\Requests\Agent\AgentTransferGoldInfoRequestRule;
use App\Http\Requests\Agent\AgentTransferGoldRequestRule;
use App\Http\Requests\Agent\AgentTransferHistoryOneRequestRule;
use App\Services\AgentService;

class AgentAPIController extends Controller
{
    private $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * @SWG\Post(
     *     path="/agent/transfer/gold",
     *     tags={"代理"},
     *     summary="代理轉出金幣API",
     *     description="代理轉出金幣給其他用戶",
     *     operationId="transferGold",
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
     *         description="與代理交易的用戶id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="gold",
     *         in="formData",
     *         description="交易金幣數量",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="comment",
     *         in="formData",
     *         description="交易備註",
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
     *                  type="object",
     *                  @SWG\Property(property="agent_gold", type="string", example="9852000.50"),
     *                  @SWG\Property(property="user_gold", type="string", example="3000.50"),
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
    public function transferGold(AgentTransferGoldRequestRule $request)
    {
        try {
            \DB::beginTransaction();
            $id = id();

            $parameters = $request->all();

            $comment = '';

            if (isset($parameters['comment'])) {
                $comment = $parameters['comment'];
            }

            $data = $this->agentService->createTransferGoldTransaction($id, $parameters['target_user_id'], $parameters['gold'], $comment);
            \DB::commit();

            return response()->success($data);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/transfer/history/one",
     *     tags={"代理"},
     *     summary="代理對單一用戶的轉帳紀錄",
     *     description="代理對單一用戶的轉帳紀錄",
     *     operationId="historyOne",
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
     *         description="與代理交易的用戶id",
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
     *                  @SWG\Property(property="history", type="array",  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="datetime", type="string", example="2019-09-18 13:39:45"),
     *                      @SWG\Property(property="gold", type="double", example=3000.50),
     *                      @SWG\Property(property="comment", type="string", example="交易的備註"),
     *                  )),
     *                  @SWG\Property(property="total", type="double", example=13500.50),
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
    public function historyOne(AgentTransferHistoryOneRequestRule $request)
    {
        try {
            $id = id();
            $parameters = $request->all();
            $data = $this->agentService->getTransferHistoryByUserId($id, $parameters['target_user_id']);
            return response()->success($data);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/name/list",
     *     tags={"代理"},
     *     summary="代理的名單列表",
     *     description="代理的名單列表",
     *     operationId="getNameList",
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
     *              property="data",
     *              type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="user_id", type="integer", example=12000002),
     *                      @SWG\Property(property="pretty_id", type="integer", example=88888888, description="显示在前端画面的id"),
     *                      @SWG\Property(property="nick_name", type="string", example="大波露"),
     *                      @SWG\Property(property="cellphone", type="string", example="0987987987"),
     *                      @SWG\Property(property="birthday", type="string", example="1900-01-01"),
     *                      @SWG\Property(property="avatar", type="string", example="http://www.cdn.com/frontcover/ZnJvbnRjb3ZlcjEyMDAwMDAz"),
     *                      @SWG\Property(property="sign", type="string", example="你好阿"),
     *                      @SWG\Property(property="sex", type="integer", example=0),
     *                      @SWG\Property(property="level", type="integer", example=0),
     *                      @SWG\Property(property="is_anchor", type="integer", example=0),
     *                      @SWG\Property(property="is_agent", type="integer", example=0),
     *                      @SWG\Property(property="is_live", type="integer", example=0),
     *                      @SWG\Property(property="is_can_withdraw", type="integer", example=1),
     *                      @SWG\Property(property="gold", type="double", example=30.50),
     *                      @SWG\Property(property="total_transport_gold", type="double", example=255.50),
     *                      @SWG\Property(property="is_star", type="integer", example=1),
     *                      @SWG\Property(property="created_at", type="", example="2019-10-05"),
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
    public function getNameList()
    {
        try {
            $id = id();
            $result = $this->agentService->getUserNameList($id);
            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/set/star",
     *     tags={"代理"},
     *     summary="代理將用戶加到最愛",
     *     description="代理將用戶加到最愛",
     *     operationId="setUserStar",
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
     *         description="要加到最愛的用戶id",
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
    public function setUserStar(AgentSetUserToStarRequestRule $request)
    {
        try {
            \DB::beginTransaction();

            $id = id();
            $parameters = $request->all();

            $this->agentService->setUserStar($id, $parameters['target_user_id']);
            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/unset/star",
     *     tags={"代理"},
     *     summary="代理將用戶移除最愛",
     *     description="代理將用戶移除最愛",
     *     operationId="unsetUserStar",
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
     *         description="要移除最愛的用戶id",
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
    public function unsetUserStar(AgentSetUserToStarRequestRule $request)
    {
        try {
            \DB::beginTransaction();

            $id = id();
            $parameters = $request->all();

            $this->agentService->unsetUserStar($id, $parameters['target_user_id']);

            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/name/list/add",
     *     tags={"代理"},
     *     summary="代理增加用戶到名單中",
     *     description="代理增加用戶到名單中",
     *     operationId="addNameList",
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
     *         description="要加入名單的用戶id",
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
    public function addNameList(AgentAddUserToNameListRequestRule $request)
    {
        try {
            \DB::beginTransaction();

            $id = id();
            $parameters = $request->all();

            $this->agentService->addUserToNameList($id, $parameters['target_user_id']);

            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/name/list/remove",
     *     tags={"代理"},
     *     summary="代理從名單中移除用戶",
     *     description="代理從名單中移除用戶",
     *     operationId="removeNameList",
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
     *         description="要移除名單的用戶id",
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
    public function removeNameList(AgentAddUserToNameListRequestRule $request)
    {
        try {
            \DB::beginTransaction();

            $id = id();
            $parameters = $request->all();
            $this->agentService->removeUserFromNameList($id, $parameters['target_user_id']);

            \DB::commit();
            return response()->success(['status' => true]);
        } catch (\Exception $ex) {
            \DB::rollback();
            return response()->error($ex);
        }
    }

    /**
     * @SWG\Post(
     *     path="/agent/transfer/gold/info",
     *     tags={"代理"},
     *     summary="代理交易總金額",
     *     description="依照日期來計算代理交易總金額, 不帶日期則取得全部交易紀錄的總金額",
     *     operationId="transferGoldInfo",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="start_date",
     *         in="formData",
     *         description="開始日期, ex: 2018-08-08",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="end_date",
     *         in="formData",
     *         description="結束日期, ex: 2018-08-08",
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
     *                  type="object",
     *                  @SWG\Property(property="total_gold", type="double", example=12312.60),
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
    public function transferGoldInfo(AgentTransferGoldInfoRequestRule $request)
    {
        try {
            $id = id();
            $parameters = $request->all();
            $start = '';
            $end = '';
            if (isset($parameters['start_date'])) {
                $start = $parameters['start_date'];
            }
            if (isset($parameters['end_date'])) {
                $end = $parameters['end_date'];
            }
            $result = $this->agentService->calculateTotalGoldByDate($id, $start, $end);

            return response()->success($result);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }
}
