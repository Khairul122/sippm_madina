<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Complaint\Rules;

use App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule;
use App\Domain\Complaint\Rules\InvalidDispositionTargetException;
use App\Domain\Complaint\ValueObjects\TargetType;
use PHPUnit\Framework\TestCase;

/**
 * BR-01/BR-02: a disposition may only ever target an OPD or a Camat.
 * Pure Domain unit test — no Laravel container/database needed.
 */
class DispositionMustTargetOpdOrCamatRuleTest extends TestCase
{
    public function test_allows_opd_target(): void
    {
        DispositionMustTargetOpdOrCamatRule::assert(TargetType::OPD);
        $this->addToAssertionCount(1);
    }

    public function test_allows_camat_target(): void
    {
        DispositionMustTargetOpdOrCamatRule::assert(TargetType::CAMAT);
        $this->addToAssertionCount(1);
    }

    public function test_rejects_bupati_target(): void
    {
        $this->expectException(InvalidDispositionTargetException::class);
        DispositionMustTargetOpdOrCamatRule::assert(TargetType::BUPATI);
    }

    public function test_rejects_wakil_bupati_target(): void
    {
        $this->expectException(InvalidDispositionTargetException::class);
        DispositionMustTargetOpdOrCamatRule::assert(TargetType::WAKIL_BUPATI);
    }

    public function test_rejects_sekda_target(): void
    {
        $this->expectException(InvalidDispositionTargetException::class);
        DispositionMustTargetOpdOrCamatRule::assert(TargetType::SEKDA);
    }
}
