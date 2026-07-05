<?php

declare(strict_types=1);

namespace App\Http\Requests\Complaint;

use App\Domain\Complaint\ValueObjects\TargetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A citizen (masyarakat) submits a new complaint. `target_type` here is
 * the ORIGINAL addressee chosen by the citizen (may be bupati/wakil_bupati/
 * sekda/opd/camat) — NOT a disposition target, so BR-01/BR-02 does not
 * apply at this step (see DisposeComplaintRequest for that enforcement).
 */
class SubmitComplaintRequest extends FormRequest
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
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'target_type' => ['required', Rule::in(array_map(fn (TargetType $t) => $t->value, TargetType::cases()))],
            'target_id' => ['nullable', 'integer'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
