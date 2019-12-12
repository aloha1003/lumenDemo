<?php
namespace App\Http\Requests\Admin\Maintain;

use Illuminate\Foundation\Http\FormRequest;

class MaintainRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
             'switch' => 'bail|nullable|string',
             'date_switch' => 'bail|nullable|string',
             'start_date' => 'bail|nullable|string',
             'end_date' => 'bail|nullable|string',
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
        //    'game_slug' => __('games.game_slug'),
          //  'password' => __('games.game_slug'),
        ];
        
    }
}
