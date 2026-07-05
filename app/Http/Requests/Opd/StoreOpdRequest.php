<?php

declare(strict_types=1);

namespace App\Http\Requests\Opd;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Kominfo-only: create a new OPD (Organisasi Perangkat Daerah) reference
 * record — used as a disposition/complaint target and to scope OPD-role
 * user accounts.
 */
class StoreOpdRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:opds,code'],
        ];
    }
}
