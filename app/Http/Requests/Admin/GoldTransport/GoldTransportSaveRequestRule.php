<?php

namespace App\Http\Requests\Admin\GoldTransport;

use Illuminate\Foundation\Http\FormRequest;

class GoldTransportSaveRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'gold_in_user_id' => 'bail|required|digits:8',
            'gold_out_user_id' => 'bail|required|digits:8',
            'transaction_gold' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
            'comment' => 'bail|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('goldTopupApplication');
    }
}
