<?php

namespace App\Http\Requests\Admin\Annouce;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'title' => 'required',
            'platform' => 'required',
            'status' => 'required',
            'content' => 'required',
            'start_at' => 'date|nullable',
            'finish_at' => 'date|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('activityAd');
    }
}
