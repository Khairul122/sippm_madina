<?php

declare(strict_types=1);

namespace App\Domain\Complaint\ValueObjects;

/**
 * Who a Complaint (original submission target) or a Disposition
 * (internal routing target) is addressed to.
 *
 * NOTE: BUPATI / WAKIL_BUPATI / SEKDA are valid as the *original* target of
 * a citizen complaint, but are NEVER valid as a *disposition* target — that
 * rule (BR-01/BR-02) is enforced by
 * App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule, not here.
 */
enum TargetType: string
{
    case BUPATI = 'bupati';
    case WAKIL_BUPATI = 'wakil_bupati';
    case SEKDA = 'sekda';
    case OPD = 'opd';
    case CAMAT = 'camat';
}
