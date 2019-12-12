<?php

namespace App\Http\Requests\Register;

use App\Http\Requests\RuleRequest;

/**
 * @OA\Post(
 *     path="/register",
 *     summary="註冊",
 *     description="註冊",
 *     operationId="register",
 *     @OA\Parameter(
 *         name="cellphone",
 *         in="path",
 *         description="用戶的手機號碼",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="path",
 *         description="密碼",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sms_code",
 *         in="path",
 *         description="驗證碼",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="請求成功"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="請求失敗"
 *     )
 * )
 */
class UserInfoRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => 'bail|required|digits:11',
            'password' => 'bail|required',
            'sms_code' => 'bail|required|digits:6'
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'cellphone' => __('message.cellphone'),
            'password' => __('message.password'),
            'sms_code' => __('message.sms_code'),
        ];
    }
}
