<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // If OTP is provided as array of digits or concatenated string, join & sanitize
        if ($this->has('otp') && is_array($this->input('otp'))) {
            $this->merge([
                'otp' => implode('', $this->input('otp')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ];
    }
}
