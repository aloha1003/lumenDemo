<?php
namespace App\Http\Requests\Admin\GameRelease;

use Illuminate\Foundation\Http\FormRequest;

class GameReleaseEditRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'game_version_id' => 'bail|required',
            'game_slug' => 'bail|required|string',
            'version' => 'bail|required|string',
            'local_download_url' => 'bail|file',
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
            'game_version_id' => __('gameRelease.game_version_id'),
            'game_slug' => __('gameRelease.game_slug'),
            'version' => __('gameRelease.version'),
            'local_download_url' => __('gameRelease.game_asset'),
            'comment' => __('gameRelease.comment'), 
        ];
        
    }
}
