<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Rules;

use DomainException;

/**
 * Thrown when a disposition is attempted against a target that is not
 * OPD or Camat (BR-01/BR-02). See DispositionMustTargetOpdOrCamatRule.
 */
final class InvalidDispositionTargetException extends DomainException
{
}
