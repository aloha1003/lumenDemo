<?php

namespace App\Http\Requests\Admin\Editor;

use Illuminate\Foundation\Http\FormRequest;

class WangRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'photo2.png' => 'image',
        ];
    }
}
