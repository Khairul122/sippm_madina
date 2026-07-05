<?php

declare(strict_types=1);

namespace App\Domain\Activity\ValueObjects;

/**
 * Lifecycle status of an Activity (kegiatan) report submitted by an
 * OPD/Camat, verified and published by Kominfo.
 */
enum ActivityStatus: string
{
    case DRAFT = 'draft';
    case DIVERIFIKASI = 'diverifikasi';
    case DIPUBLIKASIKAN = 'dipublikasikan';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DIVERIFIKASI => 'Diverifikasi',
            self::DIPUBLIKASIKAN => 'Dipublikasikan',
            self::DITOLAK => 'Ditolak',
        };
    }
}
