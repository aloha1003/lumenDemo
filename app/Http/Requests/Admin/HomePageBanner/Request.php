<?php

namespace App\Http\Requests\Admin\HomePageBanner;

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
            'cover' => 'image',
            'platform' => 'required',
            'status' => 'required',
            'content' => 'nullable',
            'start_at' => 'date|nullable',
            'finish_at' => 'date|nullable',
            'weight' => 'integer',
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
