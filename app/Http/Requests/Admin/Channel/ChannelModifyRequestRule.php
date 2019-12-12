<?php

namespace App\Http\Requests\Admin\Channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelModifyRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'channel_id' => 'bail|required',
            'name' => 'bail|required|string|max:50',
            'key_code' => 'bail|required|string|max:50',
            'official_url' => 'bail|nullable|string|max:1024',
            'ios_official_download_url' => 'bail|nullable|string|max:1024',
            'ios_official_download_cdn_url' => 'bail|nullable|string|max:1024',
            'android_official_download_url' => 'bail|nullable|string|max:1024',
            'android_official_download_cdn_url' => 'bail|nullable|string|max:1024',
            'comment' => 'bail|string|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes() 
    {
        return [
        //    'game_slug' => __('games.game_slug'),
          //  'password' => __('games.game_slug'),
        ];
        
    }
}
