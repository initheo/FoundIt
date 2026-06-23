<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $phone = $this->input('phone');
            if ($phone !== null) {
                $phone = str_replace([' ', '-'], '', $phone);
                if (str_starts_with($phone, '+62')) {
                    $phone = '0' . substr($phone, 3);
                } elseif (str_starts_with($phone, '62')) {
                    $phone = '0' . substr($phone, 2);
                }
                $this->merge([
                    'phone' => $phone,
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
                'regex:/^[a-zA-Z0-9._%+-]+@(student\.uisi\.ac\.id|uisi\.ac\.id)$/',
            ],
            'phone' => ['sometimes', 'string', 'regex:/^08[0-9]{8,11}$/', Rule::unique('users')->ignore($userId)],
            'prodi_unit' => ['sometimes', 'string', 'max:100'],
            'photo_url' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Nama maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan user lain',
            'email.regex' => 'Gunakan email UISI (@student.uisi.ac.id atau @uisi.ac.id)',
            'phone.regex' => 'Nomor HP tidak valid',
            'phone.unique' => 'Nomor HP sudah terdaftar',
            'prodi_unit.max' => 'Prodi/Unit maksimal 100 karakter',
            'photo_url.max' => 'URL foto maksimal 500 karakter',
        ];
    }
}
