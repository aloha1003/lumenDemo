<?php

namespace App\Http\Requests;

use App\Http\Requests\RuleRequest;

class Lists extends RuleRequest
{

    public function rules()
    {
        return [
            // 'id' => 'string|required|exists:test,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('test.validation.show.id.exists'),
        ];
    }

    protected function validationData()
    {
        return array_merge($this->request->all(), [
            'id' => request()->route('test'),
        ]);
    }
}