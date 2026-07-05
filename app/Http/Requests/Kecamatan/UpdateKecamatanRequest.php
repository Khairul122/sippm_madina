<?php

declare(strict_types=1);

namespace App\Http\Requests\Kecamatan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKecamatanRequest extends FormRequest
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
        $kecamatanId = $this->route('kecamatan');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('kecamatans', 'code')->ignore($kecamatanId)],
        ];
    }
}
