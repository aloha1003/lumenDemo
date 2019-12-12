<?php

namespace App\Http\Requests\Admin\Barrage;

use Illuminate\Foundation\Http\FormRequest;

class BarrageModifyRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'barrage_id' => 'bail|required|numeric',
             'name' => 'bail|required|string|max:6',
             'gold_price' => 'required|numeric|between:0.00,999999999999.99',
             'comment' => 'bail|required|string|max:20',
             'onshow' => 'bail|required',
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
