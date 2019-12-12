<?php

namespace App\Http\Requests\Admin\Game;

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
            'name' => 'required',
            'slug' => 'required|unique:games,slug,' . request()->route('id'),
            'status' => 'required',
            'cover_ios' => 'image',
            'cover_android' => 'image',
            'round_cover' => 'image',
            'options' => 'nullable',
            'game_app_id' => 'required',
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
