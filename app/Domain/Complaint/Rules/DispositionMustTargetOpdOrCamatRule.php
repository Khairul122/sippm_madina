<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Rules;

use App\Domain\Complaint\ValueObjects\TargetType;

/**
 * BR-01/BR-02: A disposition may never be routed directly to Bupati,
 * Wakil Bupati, or Sekda — only to an OPD or a Camat.
 *
 * This is the SINGLE place in the whole codebase where this rule is
 * enforced. It applies to the target of the DISPOSITION itself, not the
 * original target of the complaint: a complaint originally addressed to
 * Bupati/Wakil Bupati/Sekda must still be dispositioned by Kominfo to the
 * relevant OPD/Camat — it is never routed straight to that office.
 */
final class DispositionMustTargetOpdOrCamatRule
{
    /**
     * @throws InvalidDispositionTargetException
     */
    public static function assert(TargetType $targetType): void
    {
        if ($targetType !== TargetType::OPD && $targetType !== TargetType::CAMAT) {
            throw new InvalidDispositionTargetException(
                "Disposisi hanya boleh ditujukan ke OPD atau Camat, target diberikan: {$targetType->value}."
            );
        }
    }
}
