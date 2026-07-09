<?php

declare(strict_types=1);

namespace App\Http\Requests\Laporan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTtdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama_penandatangan' => ['required', 'string', 'max:255'],
            'jabatan_penandatangan' => ['required', 'string', 'max:255'],
            'pangkat' => ['nullable', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50'],
        ];
    }
}
