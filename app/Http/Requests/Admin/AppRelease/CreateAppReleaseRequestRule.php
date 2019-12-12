<?php


namespace App\Http\Requests\Admin\AppRelease;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppReleaseRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'channel_key_code' => 'bail|required|string|max:50',
            'ios_file' => 'bail|required|file',

            'ios_version_code' => 'bail|required|string|max:1024',
            'ios_version_number' => 'bail|required|string|max:1024',

            'android_file' => 'bail|required|file',

            'android_version_code' => 'bail|required|string|max:1024',
            'android_version_number' => 'bail|required|string|max:1024',
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
