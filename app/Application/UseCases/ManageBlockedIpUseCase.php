<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\BlockedIp;
use DomainException;
use Illuminate\Support\Facades\DB;

final class ManageBlockedIpUseCase
{
    public function block(AuditLog $log, int $actorId, string $actorIp): bool
    {
        $ip = filter_var($log->ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if ($ip === false) {
            throw new DomainException('Alamat IP pada log tidak valid atau bukan IP publik.');
        }

        $ip = inet_ntop(inet_pton($ip));
        $currentIp = filter_var($actorIp, FILTER_VALIDATE_IP);

        if ($currentIp === false) {
            throw new DomainException('IP Anda tidak dapat diverifikasi.');
        }

        if ($ip === inet_ntop(inet_pton($currentIp))) {
            throw new DomainException('IP yang sedang Anda gunakan tidak boleh diblokir.');
        }

        return DB::transaction(function () use ($ip, $log, $actorId): bool {
            $block = BlockedIp::query()->firstOrCreate(
                ['ip_address' => $ip],
                ['blocked_by' => $actorId, 'audit_log_id' => $log->id],
            );

            if (! $block->wasRecentlyCreated) {
                return false;
            }

            AuditLog::record(
                action: 'IP Diblokir',
                modelType: 'blocked_ip',
                modelId: $block->id,
                newData: ['ip_address' => $ip, 'source_audit_log_id' => $log->id],
                userId: $actorId,
            );

            return true;
        });
    }

    public function unblock(BlockedIp $block, int $actorId): void
    {
        DB::transaction(function () use ($block, $actorId): void {
            $ip = $block->ip_address;
            $block->delete();

            AuditLog::record(
                action: 'Blokir IP Dicabut',
                modelType: 'blocked_ip',
                modelId: $block->id,
                oldData: ['ip_address' => $ip],
                userId: $actorId,
            );
        });
    }
}
