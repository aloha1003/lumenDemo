<?php

namespace App\Http\Requests\Admin\PayTypeIcon;

use App\Http\Requests\RuleRequest;

class PayTypeIconRequest extends RuleRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'slug' => 'required|unique:pay_type_icons,slug,' . request()->route('id'),
            'icon' => 'image',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('payTypeIcon');
    }
}
