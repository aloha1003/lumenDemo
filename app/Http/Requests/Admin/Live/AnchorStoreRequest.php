<?php

namespace App\Http\Requests\Admin\Live;

use App\Models\AnchorInfo;
use Illuminate\Foundation\Http\FormRequest;

class AnchorStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'can_live' => 'in:' . implode(',', [AnchorInfo::CAN_LIVE_YES, AnchorInfo::CAN_LIVE_NO]),
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
