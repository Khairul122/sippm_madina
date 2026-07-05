<?php

declare(strict_types=1);

namespace App\Domain\Complaint\ValueObjects;

/**
 * Lifecycle status of a Complaint (pengaduan).
 *
 * Transition rules (BR-04) are enforced exclusively by
 * App\Domain\Complaint\Rules\StatusTransitionGuard — this enum only
 * represents the set of valid states.
 */
enum ComplaintStatus: string
{
    case DIAJUKAN = 'diajukan';
    case DIVERIFIKASI = 'diverifikasi';
    case DITOLAK = 'ditolak';
    case DIPROSES = 'diproses';
    case DITINDAKLANJUTI = 'ditindaklanjuti';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DIVERIFIKASI => 'Diverifikasi',
            self::DITOLAK => 'Ditolak',
            self::DIPROSES => 'Diproses',
            self::DITINDAKLANJUTI => 'Ditindaklanjuti',
            self::SELESAI => 'Selesai',
        };
    }
}
