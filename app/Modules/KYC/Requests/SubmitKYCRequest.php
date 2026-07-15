<?php

namespace App\Modules\KYC\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitKYCRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ktp'    => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'], // 5MB max
            'selfie' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:5120'],
            'npwp'   => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'ktp.required'    => 'ID Card (KTP) document is required.',
            'selfie.required' => 'Selfie holding KTP is required.',
            'ktp.max'         => 'KTP file size cannot exceed 5MB.',
            'selfie.max'      => 'Selfie file size cannot exceed 5MB.',
        ];
    }
}
