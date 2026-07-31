<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country_code') && $this->has('phone')) {
            $countryCode = trim($this->input('country_code'));
            $rawPhone = ltrim(trim($this->input('phone')), '+');
            
            // If rawPhone starts with countryCode digits (e.g. 628157...), strip duplicate country code
            $codeDigits = ltrim($countryCode, '+');
            if (str_starts_with($rawPhone, $codeDigits)) {
                $rawPhone = substr($rawPhone, strlen($codeDigits));
            }
            
            // Remove leading zero if present after country code (e.g., +620812 -> +62812)
            $rawPhone = ltrim($rawPhone, '0');

            $this->merge([
                'phone' => $countryCode . $rawPhone,
            ]);
        }
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
            'email.unique'       => 'Email address already registered.',
            'phone.unique'       => 'Phone number already in use.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
