<?php

declare(strict_types=1);

namespace App\Http\Requests\Kecamatan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Kominfo-only: create a new Kecamatan reference record — used as a
 * disposition/complaint target (via Camat) and to scope Camat-role user
 * accounts and Desa (village) records.
 */
class StoreKecamatanRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:kecamatans,code'],
        ];
    }
}
