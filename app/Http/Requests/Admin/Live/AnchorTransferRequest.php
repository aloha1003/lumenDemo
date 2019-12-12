<?php

namespace App\Http\Requests\Admin\Live; 
use Illuminate\Foundation\Http\FormRequest;
class AnchorTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'user_id' => 'required',
            'manager' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('anchorInfo');
    }
}
