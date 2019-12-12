<?php

namespace App\Http\Requests\Register;

use App\Http\Requests\RuleRequest;

/**
 * @OA\Post(
 *     path="/api/user/register/sms/send",
 *     summary="取得驗證碼",
 *     description="取得驗證碼",
 *     operationId="register/sms",
 *     @OA\Parameter(
 *         name="cellphone",
 *         in="path",
 *         description="用戶的手機號碼",
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
class SmsSendRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => ['bail', 'required', 'digits:11'],
            'is_resend' => 'bail|nullable|in:0,1',
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
        ];
    }

}
