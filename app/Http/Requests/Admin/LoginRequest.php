<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'captcha_api:' . request('captcha_key') . ',math'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required' => 'Captcha wajib diisi.',
            'captcha.captcha_api' => 'Captcha yang Anda masukkan salah.',
        ];
    }
}
