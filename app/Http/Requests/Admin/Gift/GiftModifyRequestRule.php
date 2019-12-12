<?php

namespace App\Http\Requests\Admin\Gift;

use Illuminate\Foundation\Http\FormRequest;

class GiftModifyRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'gift_id' => 'bail|required|numeric',
             'name' => 'bail|required|string|max:6',
             'gold_price' => 'required|numeric|between:0.00,999999999999.99',
             'comment' => 'bail|required|string|max:10',
             'image' => 'bail|image',
             'svg' => 'bail|file',

             'onshow' => 'bail|required',
             'is_prop' => 'bail|required',
             'is_mission' => 'bail|required',
             'is_big' => 'bail|required',
             
             'hot_value' => 'bail|integer|min:0|max:9999999999|required_if:is_prop,==,1',
             'hot_time' => 'bail|integer|max:9999999999|required_if:is_prop,==,1',
             'propotion_list' => [
                 'bail', 
                 'required',
                 'array',
                 'required',
                 function($attribute, $value, $fail) {
                    if (!isset($value['receive_times'])) {
                        return $fail($attribute.' is invalid.');
                    }
                    if (!isset($value['anchor_propotion'])) {
                        return $fail($attribute.' is invalid.');
                    }
                    if (!isset($value['company_propotion'])) {
                        return $fail($attribute.' is invalid.');
                    }

                    if (count($value) == 0) {
                        return $fail($attribute.' is invalid.');
                    }
                    
                    if (count($value['receive_times']) == 0) {
                        return $fail($attribute.' is invalid.');
                    }

                    if (count($value['anchor_propotion']) == 0) {
                        return $fail($attribute.' is invalid.');
                    }

                    if (count($value['company_propotion']) == 0) {
                        return $fail($attribute.' is invalid.');
                    }
                },
            ],
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
