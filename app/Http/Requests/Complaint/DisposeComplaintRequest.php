<?php

declare(strict_types=1);

namespace App\Http\Requests\Complaint;

use App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule;
use App\Domain\Complaint\Rules\InvalidDispositionTargetException;
use App\Domain\Complaint\ValueObjects\TargetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Kominfo routes a complaint to one or more OPD/Camat targets.
 *
 * `targets.*.type` is restricted to opd/camat by the `in:` rule below AS
 * WELL AS by a withValidator() closure that calls the exact same Domain
 * rule (DispositionMustTargetOpdOrCamatRule::assert()) used inside
 * DisposeComplaintUseCase — defense in depth, both layers enforce BR-01/
 * BR-02 identically instead of the Form Request re-implementing its own
 * (possibly diverging) copy of the rule.
 */
class DisposeComplaintRequest extends FormRequest
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
            'targets' => ['required', 'array', 'min:1'],
            'targets.*.type' => ['required', 'string', 'in:opd,camat'],
            'targets.*.id' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->input('targets', []) as $index => $target) {
                $type = $target['type'] ?? null;

                if ($type === null || ! in_array($type, ['opd', 'camat'], true)) {
                    continue; // already reported by the `in:` rule above
                }

                try {
                    DispositionMustTargetOpdOrCamatRule::assert(TargetType::from($type));
                } catch (InvalidDispositionTargetException $e) {
                    $validator->errors()->add("targets.{$index}.type", $e->getMessage());
                }
            }
        });
    }
}
