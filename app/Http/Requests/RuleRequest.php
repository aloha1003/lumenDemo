<?php
namespace App\Http\Requests;

use App\Exceptions\VaildateException;
use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as FormRequest;

// use Waavi\Sanitizer\Laravel\SanitizesInput;

abstract class RuleRequest extends FormRequest
{
    // use SanitizesInput;

    public function authorize()
    {
        return true;
    }

    public function validate()
    {
        if (false === $this->authorize()) {
            throw new UnauthorizedException();
        }
        $validator = app('validator')->make($this->all(), $this->rules(), $this->messages());
        if ($validator->fails()) {
            throw new VaildateException($validator->errors());
        }
    }

    abstract protected function rules();
    protected function messages()
    {
        return [];
    }
    protected function failedAuthorization()
    {
        throw new VaildateException('headers', trans('response.request_type_error'), 100002);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw_if(!$errors->isEmpty(), new VaildateException($errors->keys()[0], $errors->first()));
    }
}
