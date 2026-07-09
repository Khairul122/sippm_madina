<?php

declare(strict_types=1);

namespace App\Http\Requests\ManualBook;

use Illuminate\Foundation\Http\FormRequest;

class UploadManualBookRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }
}
