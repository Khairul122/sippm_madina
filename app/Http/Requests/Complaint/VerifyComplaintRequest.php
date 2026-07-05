<?php

declare(strict_types=1);

namespace App\Http\Requests\Complaint;

use Illuminate\Foundation\Http\FormRequest;

class VerifyComplaintRequest extends FormRequest
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
            'is_valid' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['required_if:is_valid,false', 'nullable', 'string', 'max:1000'],
        ];
    }
}
