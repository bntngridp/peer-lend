<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'full_name'   => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:20', 'unique:profiles,phone'],
            'role'        => ['required', 'in:borrower,lender'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'    => 'Email address already registered.',
            'phone.unique'    => 'Phone number already in use.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
