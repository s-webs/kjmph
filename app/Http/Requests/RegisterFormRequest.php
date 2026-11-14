<?php

namespace App\Http\Requests;

use App\Services\RegistrationGuard;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guest();
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($error = RegistrationGuard::check($this)) {
                    $validator->errors()->add('captcha', $error);
                }
            },
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'organisation' => ['required'],
            'country' => ['required'],
            'email' => ['required', 'email:dns', Rule::unique('users')],
            'password' => ['required', 'confirmed'],
        ];
    }
}
