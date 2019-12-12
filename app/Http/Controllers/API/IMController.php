<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as Controller;

class IMController extends Controller
{
    private $service;
    public function __construct()
    {

    }

    /**
     * @SWG\Post(
     *     path="/im/groupList",
     *     summary="取得大群-群组列表",
     *     description="群组列表",
     *     operationId="groupList",
     *     tags={"IM"},
     *     @SWG\ExternalDocumentation(
     *          description="腾讯云- 文档中心 ",
     *          url="http://doc.simple.api/"
     *     ),
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
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  format="int32"
     *              ),
     *               @SWG\Property(
     *                 property="message",
     *                 type="string"
     *               ),
     *               @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 description="腾讯云",
     *                 ref="#/definitions/腾讯云 IM 群组列表查询结果"
     *               )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="請求失敗",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    /**
     * @SWG\Definition(
     *     definition="腾讯云 IM 群组列表查询结果",
     *     description="群组列表查询结果",
     *     @SWG\Property(
     *          property="ActionStatus",
     *          type="string",
     *          description="请求处理的结果，OK 表示处理成功，FAIL 表示失败"
     *      ),
     *      @SWG\Property(
     *          property="ErrorCode",
     *          type="integer",
     *          format="int32",
     *          description="错误码，0表示成功，非0表示失败"
     *      ),
     *      @SWG\Property(
     *          property="ErrorInfo",
     *          type="string",
     *          description="错误信息"
     *      ),
     *      @SWG\Property(
     *          property="TotalCount",
     *          type="integer",
     *          format="int32",
     *          description="App 当前的群组总数。如果仅需要返回特定群组形态的群组，可以通过 GroupType 进行过滤，但此时返回的 TotalCount 的含义就变成了 App 中该群组形态的群组总数；
    例如：假设 App 旗下总共 50000 个群组，其中有 20000 个为公开群组，如果将请求包体中的 GroupType 设置为 Public，那么不论 Limit 和 Offset 怎样设置，应答包体中的 TotalCount 都为 20000，且 GroupIdList 中的群组全部为公开群组"
     *      ),
     *      @SWG\Property(
     *          property="GroupIdList",
     *          type="array",
     *          description="获取到的群组 ID 的集合",
     *                  @SWG\Items(
     *                      type="string",
     *                  )
     *      ),
     *      @SWG\Property(
     *          property="Next",
     *          type="integer",
     *          format="int32",
     *          description="分页拉取的标志"
     *      ),
     * )
     */
    public function groupList()
    {
        try {
            // \Live::sign("ddliveadmin");
            $queryData = [
                "Limit" => 1000,
                "Next" => 0,
                "GroupType" => "BChatRoom",
            ];
            $result = \IM::getGroupList($queryData);
            return response()->success($result, 200);
        } catch (\Exception $ex) {
            return response()->error($ex);
        }
    }

}
