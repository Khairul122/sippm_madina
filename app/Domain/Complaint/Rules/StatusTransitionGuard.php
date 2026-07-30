<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Rules;

use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use DomainException;

/**
 * BR-04: whitelist of valid Complaint status transitions, per acting role.
 *
 * Valid transitions:
 *  - DIAJUKAN        -> DIVERIFIKASI | DITOLAK   (role: kominfo)
 *  - DIVERIFIKASI    -> DIPROSES                  (role: kominfo)
 *  - DIPROSES        -> DITINDAKLANJUTI            (role: opd | camat)
 *  - DIPROSES        -> DIVERIFIKASI               (role: kominfo) — "batal
 *    disposisi": Kominfo salah pilih target OPD/Camat, tarik kembali
 *    supaya bisa didisposisikan ulang. Hanya berlaku selama status masih
 *    DIPROSES (belum ada laporan penanganan dari OPD/Camat) — begitu
 *    status berpindah ke DITINDAKLANJUTI transisi ini otomatis tidak lagi
 *    ada di whitelist, jadi tidak bisa dibatalkan setelah OPD/Camat
 *    bertindak.
 *  - DITINDAKLANJUTI -> SELESAI                    (role: kominfo)
 *
 * Any other (from, to, role) combination is invalid and throws.
 */
final class StatusTransitionGuard
{
    /**
     * @var array<string, array<string, string[]>>
     *      from-status-value => [to-status-value => [allowed role slugs]]
     */
    private const TRANSITIONS = [
        'diajukan' => [
            'diverifikasi' => ['kominfo'],
            'ditolak' => ['kominfo'],
        ],
        'diverifikasi' => [
            'diproses' => ['kominfo'],
        ],
        'diproses' => [
            'ditindaklanjuti' => ['opd', 'camat'],
            'diverifikasi' => ['kominfo'],
        ],
        'ditindaklanjuti' => [
            'selesai' => ['kominfo'],
        ],
    ];

    /**
     * @throws DomainException when the transition is not in the whitelist
     *                          for the given acting role.
     */
    public static function assertCanTransition(
        ComplaintStatus $from,
        ComplaintStatus $to,
        string $actingRoleSlug,
    ): void {
        $allowedRoles = self::TRANSITIONS[$from->value][$to->value] ?? null;

        if ($allowedRoles === null) {
            throw new DomainException(
                "Transisi status dari '{$from->value}' ke '{$to->value}' tidak valid."
            );
        }

        if (! in_array($actingRoleSlug, $allowedRoles, true)) {
            throw new DomainException(
                "Peran '{$actingRoleSlug}' tidak diizinkan mengubah status dari '{$from->value}' ke '{$to->value}'."
            );
        }
    }
}
