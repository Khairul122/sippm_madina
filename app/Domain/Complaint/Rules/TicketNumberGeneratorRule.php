<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Rules;

use App\Domain\Complaint\ValueObjects\TicketNumber;

/**
 * BR-03: ticket number format PGD-{year}-{6-digit sequence}.
 *
 * Pure function — the current max sequence for the year must be resolved
 * by the calling UseCase via ComplaintRepositoryInterface::countForYear(),
 * this Rule never queries persistence itself.
 */
final class TicketNumberGeneratorRule
{
    public static function generate(int $year, int $currentMaxSequenceForYear): TicketNumber
    {
        return TicketNumber::fromYearAndSequence($year, $currentMaxSequenceForYear + 1);
    }
}
