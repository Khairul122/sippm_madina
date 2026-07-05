<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

/**
 * The 7 RBAC roles of SIPPM Madina. slug() matches the Spatie Permission
 * role name seeded by database/seeders/RolePermissionSeeder.php.
 */
enum Role: string
{
    case MASYARAKAT = 'masyarakat';
    case KOMINFO = 'kominfo';
    case OPD = 'opd';
    case CAMAT = 'camat';
    case BUPATI = 'bupati';
    case WAKIL_BUPATI = 'wakil_bupati';
    case SEKDA = 'sekda';

    public function slug(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::MASYARAKAT => 'Masyarakat',
            self::KOMINFO => 'Kominfo',
            self::OPD => 'OPD',
            self::CAMAT => 'Camat',
            self::BUPATI => 'Bupati',
            self::WAKIL_BUPATI => 'Wakil Bupati',
            self::SEKDA => 'Sekda',
        };
    }
}
