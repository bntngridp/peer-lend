<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->user()->profile?->id;

        return [
            'full_name'      => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20', Rule::unique('profiles')->ignore($profileId)],
            'avatar'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // 2MB max
            'address'        => ['nullable', 'string', 'max:1000'],
            'city'           => ['nullable', 'string', 'max:100'],
            'province'       => ['nullable', 'string', 'max:100'],
            'occupation'     => ['nullable', 'string', 'max:255'],
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'phone.required'     => 'Nomor telepon wajib diisi untuk keamanan akun Anda.',
            'phone.unique'       => 'Nomor telepon ini sudah terdaftar pada akun lain.',
            'avatar.max'         => 'Ukuran foto profil maksimal 2MB.',
            'avatar.image'       => 'File foto profil harus berupa gambar (JPG/PNG).',
        ];
    }
}
