<?php

namespace App\Http\Requests\Admin\FrontPageAdmin;

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
            'slug' => 'required|unique:front_page_admins,slug,' . request()->route('id'),
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('frontPageAdmin');
    }
}
