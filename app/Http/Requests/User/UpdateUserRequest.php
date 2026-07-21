<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'is_active' => ['sometimes', 'boolean'],
            'opd_id' => ['nullable', 'integer', 'exists:opds,id'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($userId)],
            'nik' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nik')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
