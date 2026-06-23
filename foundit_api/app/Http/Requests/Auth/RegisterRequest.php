<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@(student\.uisi\.ac\.id|uisi\.ac\.id)$/',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^08[0-9]{8,11}$/', 'unique:users,phone'],
            'prodi_unit' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.regex' => 'Gunakan email UISI (@student.uisi.ac.id atau @uisi.ac.id)',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'phone.required' => 'Nomor HP wajib diisi',
            'phone.regex' => 'Nomor HP tidak valid',
            'phone.unique' => 'Nomor HP sudah terdaftar',
            'prodi_unit.required' => 'Prodi/Unit wajib diisi',
            'prodi_unit.max' => 'Prodi/Unit maksimal 100 karakter',
        ];
    }
}
