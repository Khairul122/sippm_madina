<?php

declare(strict_types=1);

namespace App\Http\Requests\SiteSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
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
            'hero_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hero_caption' => ['nullable', 'string', 'max:150'],
        ];
    }
}
