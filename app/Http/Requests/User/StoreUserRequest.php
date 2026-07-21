<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Domain\User\ValueObjects\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Kominfo-only: create an internal account (Kominfo/OPD/Camat/Bupati/
 * Wakil Bupati/Sekda).
 */
class StoreUserRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(array_map(fn (Role $r) => $r->value, Role::cases()))],
            'opd_id' => ['nullable', 'integer', 'exists:opds,id', 'required_if:role,opd'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id', 'required_if:role,camat'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'nik' => ['nullable', 'string', 'max:30', 'unique:users,nik'],
        ];
    }
}
