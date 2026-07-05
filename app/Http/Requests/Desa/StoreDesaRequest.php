<?php

declare(strict_types=1);

namespace App\Http\Requests\Desa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Kominfo-only: create a new Desa (village) reference record, always
 * scoped to a parent Kecamatan.
 */
class StoreDesaRequest extends FormRequest
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
            'kecamatan_id' => ['required', 'integer', 'exists:kecamatans,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('desas', 'name')->where(fn ($query) => $query->where('kecamatan_id', $this->input('kecamatan_id'))),
            ],
            'code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
