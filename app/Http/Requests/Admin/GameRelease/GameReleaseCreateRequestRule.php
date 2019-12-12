<?php

namespace App\Http\Requests\Admin\GameRelease;

use Illuminate\Foundation\Http\FormRequest;

class GameReleaseCreateRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'game_slug' => 'bail|required',
            'version' => 'bail|required|string',
            'game_asset' => 'bail|required|file',
            'comment' => 'bail|nullable|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes() 
    {
        return [
           'game_slug' => __('gameRelease.game_slug'),
           'version' => __('gameRelease.version'),
           'game_asset' => __('gameRelease.game_asset'),
           'comment' => __('gameRelease.comment'),
        ];
        
    }
}
