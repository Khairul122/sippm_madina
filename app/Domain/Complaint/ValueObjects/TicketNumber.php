<?php

declare(strict_types=1);

namespace App\Domain\Complaint\ValueObjects;

use InvalidArgumentException;

/**
 * Immutable value object for a complaint ticket number.
 *
 * Format (BR-03): PGD-{year}-{sequence padded to 6 digits}, e.g. PGD-2026-000001.
 */
final class TicketNumber
{
    private function __construct(
        private readonly int $year,
        private readonly int $sequence,
    ) {
        if ($year < 1000 || $year > 9999) {
            throw new InvalidArgumentException('TicketNumber year must be a 4-digit year.');
        }

        if ($sequence < 1 || $sequence > 999999) {
            throw new InvalidArgumentException('TicketNumber sequence must be between 1 and 999999.');
        }
    }

    public static function fromYearAndSequence(int $year, int $sequence): self
    {
        return new self($year, $sequence);
    }

    /**
     * Parse an existing formatted ticket number string, e.g. "PGD-2026-000001".
     */
    public static function fromString(string $value): self
    {
        if (! preg_match('/^PGD-(\d{4})-(\d{6})$/', $value, $matches)) {
            throw new InvalidArgumentException("Invalid ticket number format: {$value}");
        }

        return new self((int) $matches[1], (int) $matches[2]);
    }

    public function year(): int
    {
        return $this->year;
    }

    public function sequence(): int
    {
        return $this->sequence;
    }

    public function __toString(): string
    {
        return sprintf('PGD-%d-%06d', $this->year, $this->sequence);
    }
}
