<?php

declare(strict_types=1);

namespace App\Http\Requests\Activity;

use Illuminate\Foundation\Http\FormRequest;

class SubmitActivityRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'documentations' => ['nullable', 'array'],
            'documentations.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}
