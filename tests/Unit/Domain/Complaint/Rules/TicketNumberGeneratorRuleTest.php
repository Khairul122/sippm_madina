<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Complaint\Rules;

use App\Domain\Complaint\Rules\TicketNumberGeneratorRule;
use App\Domain\Complaint\ValueObjects\TicketNumber;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * BR-03: ticket number format PGD-{year}-{6-digit sequence}.
 * Pure Domain unit test for TicketNumber value object and TicketNumberGeneratorRule.
 */
class TicketNumberGeneratorRuleTest extends TestCase
{
    public function test_generates_correct_ticket_number_sequence(): void
    {
        $year = 2026;
        $currentMaxSequence = 5;

        // Rule should increment sequence: 5 + 1 = 6
        $ticket = TicketNumberGeneratorRule::generate($year, $currentMaxSequence);

        $this->assertEquals(2026, $ticket->year());
        $this->assertEquals(6, $ticket->sequence());
        $this->assertEquals('PGD-2026-000006', (string) $ticket);
    }

    public function test_generates_correct_ticket_number_sequence_for_first_item(): void
    {
        $year = 2026;
        $currentMaxSequence = 0; // No complaints yet

        // Rule should increment sequence: 0 + 1 = 1
        $ticket = TicketNumberGeneratorRule::generate($year, $currentMaxSequence);

        $this->assertEquals(2026, $ticket->year());
        $this->assertEquals(1, $ticket->sequence());
        $this->assertEquals('PGD-2026-000001', (string) $ticket);
    }

    public function test_parses_valid_ticket_number_string(): void
    {
        $ticket = TicketNumber::fromString('PGD-2026-000123');

        $this->assertEquals(2026, $ticket->year());
        $this->assertEquals(123, $ticket->sequence());
    }

    public function test_rejects_invalid_ticket_number_string_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromString('INVALID-2026-000123');
    }

    public function test_rejects_invalid_ticket_number_string_sequence_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromString('PGD-2026-123'); // Should be 6 digits
    }

    public function test_rejects_invalid_year_range_low(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromYearAndSequence(999, 1);
    }

    public function test_rejects_invalid_year_range_high(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromYearAndSequence(10000, 1);
    }

    public function test_rejects_invalid_sequence_range_low(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromYearAndSequence(2026, 0);
    }

    public function test_rejects_invalid_sequence_range_high(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TicketNumber::fromYearAndSequence(2026, 1000000);
    }
}
